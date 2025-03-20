import streamlit as st
from streamlit_extras.add_vertical_space import add_vertical_space
from .resume_analyzer import ResumeAnalyzer

class InterviewQuestionsGenerator:
    @staticmethod
    def interview_questions_prompt(query_with_chunks):
        return f'''Based on the resume content, generate a comprehensive set of interview questions tailored to the candidate's background.
        
        Structure your response in the following format:
        1. **Technical Questions**:
           - Questions specific to their technical skills and tools
           - Include scenario-based problems
           - Add follow-up questions for depth
        
        2. **Experience-Based Questions**:
           - Questions about past projects and achievements
           - Questions about challenges and solutions
           - Questions about team collaboration
        
        3. **Behavioral Questions**:
           - Questions about leadership and initiative
           - Questions about conflict resolution
           - Questions about adaptability
        
        4. **Role-Specific Questions**:
           - Questions about industry knowledge
           - Questions about methodologies used
           - Questions about best practices
        
        5. **Career Development Questions**:
           - Questions about goals and aspirations
           - Questions about continuous learning
           - Questions about preferred work environment

        For each question:
        - Include what the interviewer should look for in the answer
        - Note any red flags or positive indicators
        - Suggest appropriate follow-up questions

        Resume Content:
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        {query_with_chunks}
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        '''

    @staticmethod
    def generate_interview_questions(gemini_api_key):
        with st.form(key='InterviewQuestions'):
            add_vertical_space(1)
            pdf = st.file_uploader(label='Upload Your Resume', type='pdf')
            add_vertical_space(1)
            add_vertical_space(2)
            submit = st.form_submit_button(label='Submit')
            add_vertical_space(1)
        
        add_vertical_space(3)
        if submit:
            if pdf is not None and gemini_api_key != '':
                try:
                    with st.spinner('Processing...'):
                        pdf_chunks = ResumeAnalyzer.pdf_to_chunks(pdf)
                        interview_prompt = InterviewQuestionsGenerator.interview_questions_prompt(query_with_chunks=pdf_chunks)
                        interview_questions = ResumeAnalyzer.gemini(gemini_api_key=gemini_api_key, 
                                                                  chunks=pdf_chunks, 
                                                                  analyze=interview_prompt)

                    st.markdown(f'<h3 style="color: #1E88E5; border-bottom: 2px solid #1E88E5; padding-bottom: 8px;">Interview Questions</h3>', 
                              unsafe_allow_html=True)
                    st.markdown(interview_questions, unsafe_allow_html=True)

                except Exception as e:
                    st.markdown(f'<h5 style="text-align: center;color: orange;">{e}</h5>', 
                              unsafe_allow_html=True)

            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', 
                          unsafe_allow_html=True)
            
            elif gemini_api_key == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Gemini API Key not found in environment variables</h5>', 
                          unsafe_allow_html=True)
