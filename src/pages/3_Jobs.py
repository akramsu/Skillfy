import streamlit as st
import os
import tempfile
import PyPDF2
from resume_analyzer.job_search import JobSearch

def extract_text_from_pdf(pdf_file):
    """Extract text from uploaded PDF file"""
    with tempfile.NamedTemporaryFile(delete=False, suffix='.pdf') as tmp_file:
        tmp_file.write(pdf_file.getvalue())
        tmp_file_path = tmp_file.name
    
    text = ""
    try:
        with open(tmp_file_path, 'rb') as f:
            pdf_reader = PyPDF2.PdfReader(f)
            for page_num in range(len(pdf_reader.pages)):
                text += pdf_reader.pages[page_num].extract_text()
    except Exception as e:
        st.error(f"Error extracting text from PDF: {str(e)}")
    finally:
        os.unlink(tmp_file_path)
    
    return text

def main():
    st.title("LinkedIn Job Search")
    
    # Create tabs for different search methods
    tab1, tab2, tab3 = st.tabs(["Search by Criteria", "Search by CV", "Live LinkedIn Scraping"])
    
    # Initialize job search
    job_search = JobSearch()
    
    # Tab 1: Search by Criteria
    with tab1:
        with st.form("job_search_form"):
            col1, col2 = st.columns(2)
            
            with col1:
                job_title = st.text_input("Job Title", placeholder="e.g., Software Engineer")
                job_type = st.selectbox(
                    "Job Type",
                    ["", "Full-time", "Part-time", "Contract", "Internship", "Remote"]
                )
            
            with col2:
                location = st.text_input("Location", placeholder="e.g., San Francisco, Remote")
                experience_level = st.selectbox(
                    "Experience Level",
                    ["", "Entry level", "Mid-Senior level", "Senior level"]
                )
            
            submitted = st.form_submit_button("Search Jobs", use_container_width=True)

        # Show results when form is submitted
        if submitted and job_title:
            with st.spinner("Searching for jobs..."):
                jobs = job_search.search_jobs(
                    job_title=job_title,
                    location=location if location else None,
                    job_type=job_type if job_type else None,
                    experience_level=experience_level if experience_level else None
                )
                
                display_job_results(jobs)
    
    # Tab 2: Search by CV
    with tab2:
        st.write("Upload your CV to find matching job opportunities")
        
        uploaded_file = st.file_uploader("Upload your CV (PDF format)", type=["pdf"])
        
        if uploaded_file is not None:
            with st.spinner("Analyzing your CV..."):
                # Extract text from PDF
                cv_text = extract_text_from_pdf(uploaded_file)
                
                if cv_text:
                    # Display a preview of the extracted text
                    with st.expander("Preview extracted CV text"):
                        st.text(cv_text[:500] + "..." if len(cv_text) > 500 else cv_text)
                    
                    # Search for jobs based on CV
                    if st.button("Find Matching Jobs", use_container_width=True):
                        with st.spinner("Searching for matching jobs..."):
                            matching_jobs = job_search.search_jobs_by_cv(cv_text)
                            
                            if matching_jobs:
                                st.success(f"Found {len(matching_jobs)} jobs matching your profile!")
                                display_job_results(matching_jobs)
                            else:
                                st.warning("No matching jobs found. Try uploading a more detailed CV or search manually.")
                else:
                    st.error("Could not extract text from the uploaded PDF. Please try another file.")
    
    # Tab 3: Live LinkedIn Scraping
    with tab3:
        st.write("Search for jobs directly from LinkedIn (requires Chrome browser)")
        
        with st.form("linkedin_scrape_form"):
            col1, col2 = st.columns(2)
            
            with col1:
                linkedin_job_title = st.text_input("Job Title", placeholder="e.g., Software Engineer", key="linkedin_title")
                linkedin_job_count = st.slider("Number of Jobs to Fetch", min_value=1, max_value=10, value=5)
            
            with col2:
                linkedin_location = st.text_input("Location", placeholder="e.g., San Francisco, Remote", key="linkedin_location")
                linkedin_experience = st.selectbox(
                    "Experience Level",
                    ["", "Entry level", "Mid-Senior level", "Senior level"],
                    key="linkedin_experience"
                )
            
            linkedin_submitted = st.form_submit_button("Scrape LinkedIn Jobs", use_container_width=True)
        
        if linkedin_submitted and linkedin_job_title and linkedin_location:
            with st.spinner("Scraping LinkedIn for jobs... This may take a minute."):
                from linkedin_scraper.linkedin_scraper import LinkedInScraper
                
                scraper = LinkedInScraper()
                jobs_data = scraper.search_jobs(
                    job_title=linkedin_job_title,
                    location=linkedin_location,
                    experience_level=linkedin_experience if linkedin_experience else None
                )
                
                if jobs_data:
                    # Convert dictionary jobs to Job objects
                    jobs = [job_search._convert_to_job_object(job) for job in jobs_data]
                    st.success(f"Found {len(jobs)} jobs on LinkedIn!")
                    display_job_results(jobs)
                else:
                    st.warning("No jobs found on LinkedIn matching your criteria. Try different search terms or check your internet connection.")
    
    # Add some helpful tips
    with st.sidebar:
        st.subheader("Search Tips")
        st.markdown("""
        - Use specific job titles for better results
        - Try different locations including 'Remote'
        - Adjust experience level to see more opportunities
        - Some job types may not be available in all locations
        - For CV-based search, ensure your PDF is text-based (not scanned)
        - The LinkedIn scraper requires an active internet connection
        """)

def display_job_results(jobs):
    """Display job search results in a consistent format"""
    if jobs:
        st.subheader(f"Found {len(jobs)} matching jobs")
        
        for job in jobs:
            with st.expander(f"{job.title} at {job.company}"):
                col1, col2 = st.columns([2, 1])
                
                with col1:
                    st.markdown(f"### {job.title}")
                    st.markdown(f"**Company:** {job.company}")
                    st.markdown(f"**Location:** {job.location}")
                    st.markdown(f"**Job Type:** {job.job_type}")
                    st.markdown(f"**Experience Level:** {job.experience_level}")
                    st.markdown("### Description")
                    st.write(job.description)
                    
                    if job.requirements:
                        st.markdown("### Requirements")
                        for req in job.requirements:
                            st.markdown(f"- {req}")
                    
                    if job.benefits:
                        st.markdown("### Benefits")
                        for benefit in job.benefits:
                            st.markdown(f"- {benefit}")
                
                with col2:
                    st.markdown("### Quick Actions")
                    st.markdown(f"[Apply on LinkedIn]({job.url})")
                    st.markdown("---")
                    st.markdown("### Match Score")
                    st.progress(0.8)  # Example match score
                    st.markdown("**80% match to your profile**")
    else:
        st.info("No jobs found matching your criteria. Try adjusting your search parameters.")

if __name__ == "__main__":
    main()
