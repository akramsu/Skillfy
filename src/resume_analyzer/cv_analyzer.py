import streamlit as st
from streamlit_extras.add_vertical_space import add_vertical_space
from .resume_analyzer import ResumeAnalyzer

class CVAnalyzer:
    @staticmethod
    def cv_analysis_prompt(query_with_chunks):
        return f'''Provide a comprehensive CV analysis focusing on both content and presentation.
        
        Structure your response in the following format:
        1. **Format and Presentation**:
           - Resume structure and organization
           - Visual appeal and readability
           - Use of white space and formatting
           - Consistency in style and formatting
        
        2. **Content Analysis**:
           - Clarity and impact of professional summary
           - Quality of experience descriptions
           - Quantification of achievements
           - Use of action verbs and keywords
        
        3. **ATS Compatibility**:
           - Keyword optimization
           - Format compatibility
           - Section headings and organization
           - Potential parsing issues
        
        4. **Industry Alignment**:
           - Relevance to target industry
           - Use of industry-specific terminology
           - Demonstration of domain knowledge
           - Competitive positioning
        
        5. **Improvement Recommendations**:
           - Specific suggestions for each section
           - Content enhancements
           - Formatting improvements
           - Keywords to add
        
        6. **Overall Score** (out of 100):
           - Provide a breakdown of scores for different aspects
           - Justify the scoring
           - Compare to industry standards

        Resume Content:
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        {query_with_chunks}
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        '''

    @staticmethod
    def analyze_cv(gemini_api_key):
        with st.form(key='CVAnalysis'):
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
                        cv_prompt = CVAnalyzer.cv_analysis_prompt(query_with_chunks=pdf_chunks)
                        cv_analysis = ResumeAnalyzer.gemini(gemini_api_key=gemini_api_key, 
                                                          chunks=pdf_chunks, 
                                                          analyze=cv_prompt)

                    st.markdown(f'<h3 style="color: #1E88E5; border-bottom: 2px solid #1E88E5; padding-bottom: 8px;">CV Analysis</h3>', 
                              unsafe_allow_html=True)
                    st.markdown(cv_analysis, unsafe_allow_html=True)

                except Exception as e:
                    st.markdown(f'<h5 style="text-align: center;color: orange;">{e}</h5>', 
                              unsafe_allow_html=True)

            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', 
                          unsafe_allow_html=True)
            
            elif gemini_api_key == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Gemini API Key not found in environment variables</h5>', 
                          unsafe_allow_html=True)
