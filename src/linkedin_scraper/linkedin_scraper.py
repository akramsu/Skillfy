import time
import os
import numpy as np
import pandas as pd
import streamlit as st
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import NoSuchElementException
from streamlit_extras.add_vertical_space import add_vertical_space
from resume_analyzer.resume_analyzer import ResumeAnalyzer

class LinkedInScraper:
    @staticmethod
    def webdriver_setup():
        options = webdriver.ChromeOptions()
        options.add_argument('--headless')
        options.add_argument('--no-sandbox')
        options.add_argument('--disable-dev-shm-usage')
        # Add performance improvements
        options.add_argument('--disable-gpu')
        options.add_argument('--disable-extensions')
        options.add_argument('--disable-infobars')
        options.add_argument('--disable-notifications')
        options.add_argument('--blink-settings=imagesEnabled=false')  # Disable images
        options.add_argument('--disable-javascript')  # Disable JavaScript where possible
        options.add_argument('--incognito')  # Use incognito mode to avoid caching issues

        driver = webdriver.Chrome(options=options)
        driver.set_page_load_timeout(30)  # Set timeout to avoid hanging
        driver.maximize_window()
        return driver

    @staticmethod
    def get_userinput():
        add_vertical_space(2)
        
        # Create tabs for different search methods
        search_tab, resume_tab = st.tabs(["Search by Title", "Search by Resume"])
        
        with search_tab:
            with st.form(key='linkedin_search'):
                add_vertical_space(1)
                col1, col2, col3 = st.columns([0.5, 0.3, 0.2], gap='medium')
                with col1:
                    job_title_input = st.text_input(label='Job Title')
                    job_title_input = job_title_input.split(',')
                with col2:
                    job_location = st.text_input(label='Job Location', value='India')
                with col3:
                    job_count = st.number_input(label='Job Count', min_value=1, value=1, step=1)

                add_vertical_space(1)
                search_submit = st.form_submit_button(label='Search')
                add_vertical_space(1)
        
        with resume_tab:
            with st.form(key='resume_search'):
                add_vertical_space(1)
                pdf = st.file_uploader(label='Upload Your Resume', type='pdf')
                col1, col2 = st.columns([0.7, 0.3], gap='medium')
                with col1:
                    job_location = st.text_input(label='Preferred Location', value='India')
                with col2:
                    job_count = st.number_input(label='Number of Jobs', min_value=1, value=5, step=1)
                
                add_vertical_space(1)
                resume_submit = st.form_submit_button(label='Find Matching Jobs')
                add_vertical_space(1)
                
        return {
            'search': {
                'submit': search_submit,
                'job_titles': job_title_input if search_submit else None,
                'location': job_location if search_submit else None,
                'count': job_count if search_submit else None
            },
            'resume': {
                'submit': resume_submit,
                'pdf': pdf if resume_submit else None,
                'location': job_location if resume_submit else None,
                'count': job_count if resume_submit else None
            }
        }

    @staticmethod
    def extract_job_titles_prompt(query_with_chunks):
        return f'''Based on the resume content, identify the most relevant job titles that match the candidate's skills and experience.
        
        Return ONLY a comma-separated list of 3-5 job titles, without any additional text or formatting.
        The job titles should be:
        1. Specific and commonly used in job postings
        2. Aligned with the candidate's experience level
        3. Relevant to their technical skills and domain expertise
        
        Resume Content:
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        {query_with_chunks}
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        '''

    @staticmethod
    def build_url(job_title, job_location):
        b = []
        for i in job_title:
            x = i.split()
            y = '%20'.join(x)
            b.append(y)

        job_title = '%2C%20'.join(b)
        link = f"https://in.linkedin.com/jobs/search?keywords={job_title}&location={job_location}&locationId=&geoId=102713980&f_TPR=r604800&position=1&pageNum=0"
        return link

    @staticmethod
    def open_link(driver, link):
        # Set a maximum number of retries to avoid infinite loops
        max_retries = 3
        retries = 0
        
        while retries < max_retries:
            try:
                driver.get(link)
                # Reduced wait time
                driver.implicitly_wait(3)
                time.sleep(1)  # Reduced sleep time
                driver.find_element(by=By.CSS_SELECTOR, value='span.switcher-tabs__placeholder-text.m-auto')
                return True
            
            except NoSuchElementException:
                retries += 1
                if retries >= max_retries:
                    st.warning(f"Failed to load page after {max_retries} attempts. Continuing with limited functionality.")
                    return False
                continue

    @staticmethod
    def link_open_scrolldown(driver, link, job_count):
        # Open the Link in LinkedIn
        success = LinkedInScraper.open_link(driver, link)
        if not success:
            return
            
        # Calculate scroll iterations based on job count
        # More jobs require more scrolling, but we can optimize
        scroll_iterations = min(job_count * 2, 10)  # Cap at 10 to avoid excessive scrolling
        
        # Scroll Down the Page more efficiently
        for i in range(scroll_iterations):
            # Dismiss sign-in modal if present
            try:
                driver.find_element(by=By.CSS_SELECTOR, 
                                value="button[data-tracking-control-name='public_jobs_contextual-sign-in-modal_modal_dismiss']>icon>svg").click()
            except:
                pass

            # Scroll down in larger increments
            driver.execute_script(f"window.scrollBy(0, {1000});")
            
            # Click on See More Jobs Button if Present, but don't wait too long
            try:
                driver.find_element(by=By.CSS_SELECTOR, value="button[aria-label='See more jobs']").click()
                driver.implicitly_wait(2)  # Reduced wait time
            except:
                pass
            
            # Short pause between scrolls
            time.sleep(0.5)  # Reduced sleep time

    @staticmethod
    def job_title_filter(scrap_job_title, user_job_title_input):
        # User Job Title Convert into Lower Case
        user_input = [i.lower().strip() for i in user_job_title_input]

        # scraped Job Title Convert into Lower Case
        scrap_title = scrap_job_title.lower().strip()

        # Verify Any User Job Title in the scraped Job Title
        for i in user_input:
            # Check if all words in the user input are in the scraped title
            if all(word in scrap_title for word in i.split()):
                return scrap_job_title
                
        return np.nan

    @staticmethod
    def scrap_company_data(driver, job_title_input, job_location):
        try:
            # Get all job cards at once
            job_cards = driver.find_elements(by=By.CSS_SELECTOR, value='.base-card')
            
            company_names = []
            job_titles = []
            locations = []
            urls = []
            
            # Process each job card
            for card in job_cards:
                try:
                    # Extract data from each card
                    company_name = card.find_element(by=By.CSS_SELECTOR, value='.base-search-card__subtitle').text
                    job_title = card.find_element(by=By.CSS_SELECTOR, value='.base-search-card__title').text
                    location = card.find_element(by=By.CSS_SELECTOR, value='.job-search-card__location').text
                    url = card.find_element(by=By.CSS_SELECTOR, value='a').get_attribute('href')
                    
                    company_names.append(company_name)
                    job_titles.append(job_title)
                    locations.append(location)
                    urls.append(url)
                except:
                    # Skip cards with missing data
                    continue
            
            # Create DataFrame
            df = pd.DataFrame({
                'Company Name': company_names,
                'Job Title': job_titles,
                'Location': locations,
                'Website URL': urls
            })
            
            # Apply filters more efficiently
            # Filter job titles
            df['Job Title'] = df['Job Title'].apply(lambda x: LinkedInScraper.job_title_filter(x, job_title_input))
            
            # Filter locations
            job_location_lower = job_location.lower()
            df['Location'] = df['Location'].apply(lambda x: x if job_location_lower in x.lower() else np.nan)
            
            # Drop rows with NaN values and reset index
            df = df.dropna()
            df.reset_index(drop=True, inplace=True)
            
            return df
            
        except Exception as e:
            st.error(f"Error scraping job data: {e}")
            return pd.DataFrame(columns=['Company Name', 'Job Title', 'Location', 'Website URL'])

    @staticmethod
    def scrap_job_description(driver, df, job_count):
        if df.empty:
            return df
            
        # Get URL into List
        website_url = df['Website URL'].tolist()
        
        # Limit to requested job count
        website_url = website_url[:job_count]
        
        # Create progress bar
        progress_bar = st.progress(0)
        status_text = st.empty()
        
        # Initialize lists for new columns
        job_description = []
        job_type = []
        experience_level = []
        job_function = []
        industries = []
        
        # Scrap Job Description
        for i, url in enumerate(website_url):
            try:
                # Update progress
                progress = int((i + 1) * 100 / len(website_url))
                progress_bar.progress(progress)
                status_text.text(f"Processing job {i + 1} of {len(website_url)}")
                
                # Open Job URL
                driver.get(url)
                time.sleep(1)
                
                try:
                    description = driver.find_element(by=By.CLASS_NAME, value='description__text').text
                    job_description.append(description)
                except:
                    job_description.append('Not Found')
                
                try:
                    type_text = driver.find_element(by=By.CLASS_NAME, value='description__job-criteria-text').text
                    job_type.append(type_text)
                except:
                    job_type.append('Not Found')
                
                try:
                    experience = driver.find_element(by=By.XPATH, value="//span[contains(@class, 'description__job-criteria-text')][2]").text
                    experience_level.append(experience)
                except:
                    experience_level.append('Not Found')
                
                try:
                    function = driver.find_element(by=By.XPATH, value="//span[contains(@class, 'description__job-criteria-text')][3]").text
                    job_function.append(function)
                except:
                    job_function.append('Not Found')
                
                try:
                    industry = driver.find_element(by=By.XPATH, value="//span[contains(@class, 'description__job-criteria-text')][4]").text
                    industries.append(industry)
                except:
                    industries.append('Not Found')
                    
            except Exception as e:
                print(f"Error processing URL {url}: {str(e)}")
                job_description.append('Error')
                job_type.append('Error')
                experience_level.append('Error')
                job_function.append('Error')
                industries.append('Error')
        
        # Clear progress bar and status text
        progress_bar.empty()
        status_text.empty()
        
        # Add new columns to DataFrame
        df_subset = df.iloc[:len(website_url)].copy()
        df_subset['Job Description'] = job_description
        df_subset['Job Type'] = job_type
        df_subset['Experience Level'] = experience_level
        df_subset['Job Function'] = job_function
        df_subset['Industries'] = industries
        
        return df_subset

    @staticmethod
    def display_data_userinterface(df_final):
        if df_final.empty:
            st.error("No jobs found matching your criteria. Please try different search terms.")
            return
            
        st.markdown(f'<h3 style="color: #1E88E5; border-bottom: 2px solid #1E88E5; padding-bottom: 8px;">LinkedIn Jobs</h3>', unsafe_allow_html=True)
        st.dataframe(df_final)
        
        csv = df_final.to_csv(index=False).encode('utf-8')
        st.download_button(
            "Download CSV",
            csv,
            "linkedin_jobs.csv",
            "text/csv",
            key='download-csv'
        )

    @staticmethod
    def main():
        user_input = LinkedInScraper.get_userinput()
        
        # Handle search by title
        if user_input['search']['submit']:
            job_titles = user_input['search']['job_titles']
            location = user_input['search']['location']
            count = user_input['search']['count']
            
            if job_titles[0] != '' and location != '' and count > 0:
                try:
                    with st.spinner('Searching jobs...'):
                        driver = LinkedInScraper.webdriver_setup()
                        
                        url = LinkedInScraper.build_url(job_titles, location)
                        LinkedInScraper.link_open_scrolldown(driver, url, count)
                        
                        df = LinkedInScraper.scrap_company_data(driver, job_titles, location)
                        if not df.empty:
                            df_final = LinkedInScraper.scrap_job_description(driver, df, count)
                            LinkedInScraper.display_data_userinterface(df_final)
                        else:
                            st.error("No matching jobs found. Please try different search terms.")
                            
                        driver.quit()
                        
                except Exception as e:
                    st.error(f"An error occurred: {str(e)}")
                    
            elif job_titles[0] == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Enter Job Title</h5>', unsafe_allow_html=True)
            elif location == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Enter Job Location</h5>', unsafe_allow_html=True)
            elif count == 0:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Enter Number of Jobs Greater Than 0</h5>', unsafe_allow_html=True)
        
        # Handle search by resume
        elif user_input['resume']['submit']:
            pdf = user_input['resume']['pdf']
            location = user_input['resume']['location']
            count = user_input['resume']['count']
            
            if pdf is not None and location != '' and count > 0:
                try:
                    with st.spinner('Analyzing resume and searching for matching jobs...'):
                        # Extract job titles from resume
                        pdf_chunks = ResumeAnalyzer.pdf_to_chunks(pdf)
                        prompt = LinkedInScraper.extract_job_titles_prompt(query_with_chunks=pdf_chunks)
                        job_titles = ResumeAnalyzer.gemini(gemini_api_key=os.getenv("GEMINI_API_KEY"), 
                                                         chunks=pdf_chunks, 
                                                         analyze=prompt)
                        
                        # Convert job titles string to list
                        job_titles = [title.strip() for title in job_titles.split(',')]
                        
                        st.info(f"Based on your resume, searching for the following positions: {', '.join(job_titles)}")
                        
                        # Search for jobs
                        driver = LinkedInScraper.webdriver_setup()
                        url = LinkedInScraper.build_url(job_titles, location)
                        LinkedInScraper.link_open_scrolldown(driver, url, count)
                        
                        df = LinkedInScraper.scrap_company_data(driver, job_titles, location)
                        if not df.empty:
                            df_final = LinkedInScraper.scrap_job_description(driver, df, count)
                            LinkedInScraper.display_data_userinterface(df_final)
                        else:
                            st.error("No matching jobs found. Please try uploading a different resume or searching in a different location.")
                            
                        driver.quit()
                        
                except Exception as e:
                    st.error(f"An error occurred: {str(e)}")
            
            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', unsafe_allow_html=True)
            elif location == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Enter Job Location</h5>', unsafe_allow_html=True)
            elif count == 0:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Enter Number of Jobs Greater Than 0</h5>', unsafe_allow_html=True)
