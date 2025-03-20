import os
import streamlit as st
from PyPDF2 import PdfReader
from langchain.text_splitter import RecursiveCharacterTextSplitter
from langchain_google_genai import GoogleGenerativeAIEmbeddings
from langchain.vectorstores import FAISS
import google.generativeai as genai
from streamlit_extras.add_vertical_space import add_vertical_space

class ResumeAnalyzer:
    @staticmethod
    def pdf_to_chunks(pdf):
        pdf_reader = PdfReader(pdf)
        text = ""
        for page in pdf_reader.pages:
            text += page.extract_text()

        text_splitter = RecursiveCharacterTextSplitter(
            chunk_size=700,
            chunk_overlap=200,
            length_function=len)

        chunks = text_splitter.split_text(text=text)
        return chunks

    @staticmethod
    def gemini(gemini_api_key, chunks, analyze):
        try:
            os.environ["GOOGLE_API_KEY"] = gemini_api_key
            
            embeddings = GoogleGenerativeAIEmbeddings(
                model="models/embedding-001",
            )

            vectorstores = FAISS.from_texts(chunks, embedding=embeddings)
            docs = vectorstores.similarity_search(query=analyze, k=3)
            doc_content = "\n\n".join([doc.page_content for doc in docs])
            
            generation_config = {
                "temperature": 0.7,
                "top_p": 1,
                "top_k": 1,
                "max_output_tokens": 1000,
            }
            
            model = genai.GenerativeModel(
                model_name="models/gemini-1.5-pro-latest",
                generation_config=generation_config,
            )
            
            prompt = f"""
            Based on the following resume information:
            
            {doc_content}
            
            Please answer the following question:
            {analyze}
            """
            
            response = model.generate_content(prompt)
            return response.text
            
        except Exception as e:
            print(f"Error with Gemini API: {str(e)}")
            return f"Error with Gemini API: {str(e)}"

    @staticmethod
    def summary_prompt(query_with_chunks):
        return f'''Provide a comprehensive and detailed analysis of the following resume. 
        
        Structure your response in the following format:
        1. **Professional Summary**: A concise overview of the candidate's background, experience, and key qualifications.
        2. **Key Skills**: List the top technical and soft skills evident in the resume.
        3. **Experience Highlights**: Summarize the most significant professional experiences and achievements.
        4. **Education & Certifications**: Highlight educational background and relevant certifications.
        5. **Overall Assessment**: Provide a final evaluation of the candidate's profile and potential fit for roles.

        Format each section with proper headings and bullet points for better readability.

        Resume Content:
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        {query_with_chunks}
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        '''

    @staticmethod
    def strength_prompt(query_with_chunks):
        return f'''Analyze the strengths in the following resume and provide a detailed assessment.
        
        Structure your response in the following format:
        1. **Technical Strengths**: Identify and analyze the technical skills, tools, and technologies the candidate excels in.
        2. **Professional Strengths**: Highlight strong professional experiences, achievements, and responsibilities.
        3. **Soft Skills & Qualities**: Identify communication, leadership, problem-solving, and other soft skills evident in the resume.
        4. **Educational Strengths**: Note any impressive educational qualifications, certifications, or specialized training.
        5. **Unique Selling Points**: Identify what makes this candidate stand out from others in their field.

        Resume Content:
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        {query_with_chunks}
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        '''

    @staticmethod
    def resume_summary(gemini_api_key):
        st.markdown(f'<h4 style="text-align: center;">Get a comprehensive summary of your resume</h4>', 
                  unsafe_allow_html=True)
        add_vertical_space(1)
        
        with st.form(key='ResumeSummary'):
            add_vertical_space(1)
            pdf = st.file_uploader(label='Upload Your Resume', type='pdf')
            add_vertical_space(2)
            submit = st.form_submit_button(label='Get Summary')
            add_vertical_space(1)
        
        add_vertical_space(2)
        if submit:
            if pdf is not None and gemini_api_key != '':
                try:
                    with st.spinner('Analyzing your resume...'):
                        pdf_chunks = ResumeAnalyzer.pdf_to_chunks(pdf)
                        summary_prompt = ResumeAnalyzer.resume_summary_prompt(query_with_chunks=pdf_chunks)
                        summary = ResumeAnalyzer.gemini(
                            gemini_api_key=gemini_api_key,
                            chunks=pdf_chunks,
                            analyze=summary_prompt
                        )

                    st.markdown(f'<h3 style="color: #1E88E5; border-bottom: 2px solid #1E88E5; padding-bottom: 8px;">Resume Summary</h3>', 
                              unsafe_allow_html=True)
                    st.markdown(summary, unsafe_allow_html=True)

                except Exception as e:
                    st.markdown(f'<h5 style="text-align: center;color: orange;">{e}</h5>', 
                              unsafe_allow_html=True)

            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', 
                          unsafe_allow_html=True)
            
            elif gemini_api_key == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Gemini API Key not found in environment variables</h5>', 
                          unsafe_allow_html=True)

    @staticmethod
    def analyze_strength(gemini_api_key):
        st.markdown(f'<h4 style="text-align: center;">Identify key strengths in your resume</h4>', 
                  unsafe_allow_html=True)
        add_vertical_space(1)
        
        with st.form(key='ResumeStrength'):
            add_vertical_space(1)
            pdf = st.file_uploader(label='Upload Your Resume', type='pdf')
            add_vertical_space(2)
            submit = st.form_submit_button(label='Analyze Strengths')
            add_vertical_space(1)
        
        add_vertical_space(2)
        if submit:
            if pdf is not None and gemini_api_key != '':
                try:
                    with st.spinner('Analyzing your strengths...'):
                        pdf_chunks = ResumeAnalyzer.pdf_to_chunks(pdf)
                        strength_prompt = ResumeAnalyzer.strength_prompt(query_with_chunks=pdf_chunks)
                        strengths = ResumeAnalyzer.gemini(
                            gemini_api_key=gemini_api_key,
                            chunks=pdf_chunks,
                            analyze=strength_prompt
                        )

                    st.markdown(f'<h3 style="color: #1E88E5; border-bottom: 2px solid #1E88E5; padding-bottom: 8px;">Key Strengths</h3>', 
                              unsafe_allow_html=True)
                    st.markdown(strengths, unsafe_allow_html=True)

                except Exception as e:
                    st.markdown(f'<h5 style="text-align: center;color: orange;">{e}</h5>', 
                              unsafe_allow_html=True)

            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', 
                          unsafe_allow_html=True)
            
            elif gemini_api_key == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Gemini API Key not found in environment variables</h5>', 
                          unsafe_allow_html=True)
