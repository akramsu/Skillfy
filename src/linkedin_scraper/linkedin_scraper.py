import time
import os
import numpy as np
import pandas as pd
import streamlit as st
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import NoSuchElementException, TimeoutException, WebDriverException
from streamlit_extras.add_vertical_space import add_vertical_space
from resume_analyzer.resume_analyzer import ResumeAnalyzer
from typing import List, Dict, Optional

class LinkedInScraper:
    def __init__(self):
        self.driver = None
        self.base_url = "https://www.linkedin.com/jobs/search"
        self.jobs_data = []

    def webdriver_setup(self):
        """Set up the Chrome WebDriver with optimized settings"""
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
        options.add_argument('--incognito')  # Use incognito mode to avoid caching issues
        
        # Add user agent to avoid detection
        options.add_argument('--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')

        try:
            self.driver = webdriver.Chrome(options=options)
            self.driver.set_page_load_timeout(30)  # Set timeout to avoid hanging
            self.driver.maximize_window()
            return True
        except WebDriverException as e:
            st.error(f"Failed to initialize WebDriver: {str(e)}")
            return False

    def close_driver(self):
        """Safely close the WebDriver"""
        if self.driver:
            try:
                self.driver.quit()
            except Exception:
                pass
            self.driver = None

    def search_jobs(self, job_title: str, location: Optional[str] = None, job_type: Optional[str] = None, 
                   experience_level: Optional[str] = None) -> List[Dict]:
        """
        Search for jobs on LinkedIn based on given criteria
        """
        if not self.driver and not self.webdriver_setup():
            return []

        try:
            # Construct the search URL
            search_url = f"{self.base_url}/?keywords={job_title.replace(' ', '%20')}"
            if location:
                search_url += f"&location={location.replace(' ', '%20')}"
            
            # Add filters for job type and experience level if provided
            if job_type or experience_level:
                search_url += "&f_WT=2" if job_type == "Remote" else ""
                
                if experience_level == "Entry level":
                    search_url += "&f_E=1"
                elif experience_level == "Mid-Senior level":
                    search_url += "&f_E=2,3"
                elif experience_level == "Senior level":
                    search_url += "&f_E=4,5,6"
            
            # Navigate to the search URL
            self.driver.get(search_url)
            
            # Wait for the job listings to load
            try:
                WebDriverWait(self.driver, 10).until(
                    EC.presence_of_element_located((By.CSS_SELECTOR, ".jobs-search__results-list"))
                )
            except TimeoutException:
                st.warning("Timeout waiting for job listings to load. LinkedIn might be rate-limiting requests.")
                return []
            
            # Extract job listings
            job_listings = self.driver.find_elements(By.CSS_SELECTOR, ".jobs-search__results-list li")
            
            # If no jobs found, return empty list
            if not job_listings:
                return []
            
            # Process each job listing
            jobs_data = []
            for job in job_listings[:10]:  # Limit to first 10 jobs to avoid long processing times
                try:
                    # Extract job details
                    title_element = job.find_element(By.CSS_SELECTOR, "h3.base-search-card__title")
                    company_element = job.find_element(By.CSS_SELECTOR, "h4.base-search-card__subtitle")
                    location_element = job.find_element(By.CSS_SELECTOR, "span.job-search-card__location")
                    link_element = job.find_element(By.CSS_SELECTOR, "a.base-card__full-link")
                    
                    job_url = link_element.get_attribute("href")
                    
                    # Get detailed job info
                    job_details = self.get_job_details(job_url)
                    
                    # Create job data dictionary
                    job_data = {
                        "title": title_element.text.strip(),
                        "company": company_element.text.strip(),
                        "location": location_element.text.strip(),
                        "url": job_url,
                        "description": job_details.get("description", "No description available"),
                        "job_type": job_details.get("job_type", job_type if job_type else "Not specified"),
                        "experience_level": job_details.get("experience_level", experience_level if experience_level else "Not specified"),
                        "requirements": job_details.get("requirements", []),
                        "benefits": job_details.get("benefits", [])
                    }
                    
                    jobs_data.append(job_data)
                except NoSuchElementException:
                    continue
                except Exception as e:
                    st.error(f"Error processing job listing: {str(e)}")
                    continue
            
            return jobs_data
            
        except Exception as e:
            st.error(f"Error during job search: {str(e)}")
            return []
        finally:
            self.close_driver()

    def get_job_details(self, job_url: str) -> Dict:
        """
        Get detailed information about a specific job
        """
        try:
            # Open a new tab for job details
            self.driver.execute_script("window.open('');")
            self.driver.switch_to.window(self.driver.window_handles[1])
            self.driver.get(job_url)
            
            # Wait for job details to load
            try:
                WebDriverWait(self.driver, 10).until(
                    EC.presence_of_element_located((By.CSS_SELECTOR, ".jobs-description__content"))
                )
            except TimeoutException:
                # Close tab and switch back
                self.driver.close()
                self.driver.switch_to.window(self.driver.window_handles[0])
                return {}
            
            # Extract job description
            description = ""
            try:
                description_element = self.driver.find_element(By.CSS_SELECTOR, ".jobs-description__content")
                description = description_element.text.strip()
            except NoSuchElementException:
                description = "No description available"
            
            # Extract job type
            job_type = "Not specified"
            try:
                job_details = self.driver.find_elements(By.CSS_SELECTOR, ".jobs-unified-top-card__job-insight")
                for detail in job_details:
                    if "Employment type" in detail.text:
                        job_type = detail.text.replace("Employment type", "").strip()
                        break
            except NoSuchElementException:
                pass
            
            # Extract requirements and benefits from description
            requirements = []
            benefits = []
            
            description_lines = description.split('\n')
            in_requirements_section = False
            in_benefits_section = False
            
            for line in description_lines:
                line = line.strip()
                if not line:
                    continue
                
                if "requirements" in line.lower() or "qualifications" in line.lower():
                    in_requirements_section = True
                    in_benefits_section = False
                    continue
                
                if "benefits" in line.lower() or "perks" in line.lower() or "offer" in line.lower():
                    in_benefits_section = True
                    in_requirements_section = False
                    continue
                
                if in_requirements_section and line.startswith("•"):
                    requirements.append(line.replace("•", "").strip())
                
                if in_benefits_section and line.startswith("•"):
                    benefits.append(line.replace("•", "").strip())
            
            # If no requirements or benefits were found, try to extract them using AI
            if not requirements or not benefits:
                # This would be a good place to use AI to extract requirements and benefits
                # For now, we'll just provide some generic ones
                if not requirements:
                    requirements = [
                        "Experience in the relevant field",
                        "Communication skills",
                        "Problem-solving abilities"
                    ]
                
                if not benefits:
                    benefits = [
                        "Competitive salary",
                        "Professional development opportunities",
                        "Work-life balance"
                    ]
            
            # Close tab and switch back
            self.driver.close()
            self.driver.switch_to.window(self.driver.window_handles[0])
            
            return {
                "description": description,
                "job_type": job_type,
                "requirements": requirements,
                "benefits": benefits
            }
            
        except Exception as e:
            # Make sure to close the tab and switch back in case of error
            try:
                self.driver.close()
                self.driver.switch_to.window(self.driver.window_handles[0])
            except:
                pass
            return {}

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
                        scraper = LinkedInScraper()
                        jobs_data = scraper.search_jobs(job_title=job_titles[0], location=location)
                        
                        if jobs_data:
                            df = pd.DataFrame(jobs_data)
                            LinkedInScraper.display_data_userinterface(df)
                        else:
                            st.error("No matching jobs found. Please try different search terms.")
                            
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
                        scraper = LinkedInScraper()
                        jobs_data = scraper.search_jobs(job_title=job_titles[0], location=location)
                        
                        if jobs_data:
                            df = pd.DataFrame(jobs_data)
                            LinkedInScraper.display_data_userinterface(df)
                        else:
                            st.error("No matching jobs found. Please try uploading a different resume or searching in a different location.")
                            
                except Exception as e:
                    st.error(f"An error occurred: {str(e)}")
            
            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', unsafe_allow_html=True)
            elif location == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Enter Job Location</h5>', unsafe_allow_html=True)
            elif count == 0:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Enter Number of Jobs Greater Than 0</h5>', unsafe_allow_html=True)

if __name__ == "__main__":
    LinkedInScraper.main()
