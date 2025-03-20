import streamlit as st
from streamlit_extras.add_vertical_space import add_vertical_space
from .resume_analyzer import ResumeAnalyzer

class WeaknessAnalyzer:
    @staticmethod
    def weakness_prompt(query_with_chunks):
        return f'''Analyze the weaknesses and areas of improvement in the following resume and provide a detailed assessment.
        
        Structure your response in the following format:
        1. **Technical Gaps**: Identify missing or outdated technical skills that could be valuable in their field.
        2. **Experience Gaps**: Point out any gaps in work history or areas where experience could be strengthened.
        3. **Professional Development Needs**: Suggest certifications, courses, or skills that could enhance their profile.
        4. **Resume Presentation**: Identify any issues with resume formatting, structure, or content presentation.
        5. **Improvement Recommendations**: Provide actionable recommendations to address each identified weakness.

        Format the response with clear headings and bullet points for better readability.
        Be constructive and focus on opportunities for growth rather than criticism.

        Resume Content:
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        {query_with_chunks}
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        '''

    @staticmethod
    def analyze_weakness(gemini_api_key):
        with st.form(key='Weakness'):
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
                        weakness_prompt = WeaknessAnalyzer.weakness_prompt(query_with_chunks=pdf_chunks)
                        weakness_analysis = ResumeAnalyzer.gemini(gemini_api_key=gemini_api_key, 
                                                                chunks=pdf_chunks, 
                                                                analyze=weakness_prompt)

                    st.markdown(f'<h3 style="color: #1E88E5; border-bottom: 2px solid #1E88E5; padding-bottom: 8px;">Weakness Analysis</h3>', 
                              unsafe_allow_html=True)
                    st.markdown(weakness_analysis, unsafe_allow_html=True)

                except Exception as e:
                    st.markdown(f'<h5 style="text-align: center;color: orange;">{e}</h5>', 
                              unsafe_allow_html=True)

            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', 
                          unsafe_allow_html=True)
            
            elif gemini_api_key == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Gemini API Key not found in environment variables</h5>', 
                          unsafe_allow_html=True)
