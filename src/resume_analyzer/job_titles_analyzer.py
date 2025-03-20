import streamlit as st
from streamlit_extras.add_vertical_space import add_vertical_space
from .resume_analyzer import ResumeAnalyzer

class JobTitlesAnalyzer:
    @staticmethod
    def job_titles_prompt(query_with_chunks):
        return f'''Based on the resume content, suggest relevant job titles and roles that match the candidate's skills and experience.
        
        Follow these rules:
        1. List 5-7 most suitable job titles
        2. For each job title, provide:
           - Job title name
           - Brief explanation why it's a good fit
           - Key skills from the resume that match this role
           - Suggested next steps to strengthen qualifications
        3. Order from most relevant to least relevant
        4. Include both technical and managerial roles if applicable
        5. Consider both current skills and potential growth areas
        
        Format your response in markdown with sections:
        1. **Primary Job Titles** (Most relevant based on current skills)
        2. **Secondary Job Titles** (Roles requiring some upskilling)
        3. **Career Progression Path** (Future roles with additional experience)
        
        Resume Content:
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        {query_with_chunks}
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        '''

    @staticmethod
    def suggest_job_titles(gemini_api_key):
        st.markdown(f'<h4 style="text-align: center;">Get suggested job titles based on your resume</h4>', 
                  unsafe_allow_html=True)
        add_vertical_space(1)
        
        with st.form(key='JobTitles'):
            add_vertical_space(1)
            pdf = st.file_uploader(label='Upload Your Resume', type='pdf')
            add_vertical_space(2)
            submit = st.form_submit_button(label='Get Job Suggestions')
            add_vertical_space(1)
        
        add_vertical_space(2)
        if submit:
            if pdf is not None and gemini_api_key != '':
                try:
                    with st.spinner('Analyzing your resume and finding suitable job titles...'):
                        pdf_chunks = ResumeAnalyzer.pdf_to_chunks(pdf)
                        titles_prompt = JobTitlesAnalyzer.job_titles_prompt(query_with_chunks=pdf_chunks)
                        titles = ResumeAnalyzer.gemini(
                            gemini_api_key=gemini_api_key,
                            chunks=pdf_chunks,
                            analyze=titles_prompt
                        )

                    st.markdown(f'<h3 style="color: #1E88E5; border-bottom: 2px solid #1E88E5; padding-bottom: 8px;">Suggested Job Titles</h3>', 
                              unsafe_allow_html=True)
                    st.markdown(titles, unsafe_allow_html=True)
                    
                    st.info("💡 Pro Tips:\n"
                           "- Use these titles as keywords in your job search\n"
                           "- Tailor your resume for each specific role\n"
                           "- Research salary ranges for these positions\n"
                           "- Look for job postings with these exact titles")

                except Exception as e:
                    st.markdown(f'<h5 style="text-align: center;color: orange;">{e}</h5>', 
                              unsafe_allow_html=True)

            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', 
                          unsafe_allow_html=True)
            
            elif gemini_api_key == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Gemini API Key not found in environment variables</h5>', 
                          unsafe_allow_html=True)
