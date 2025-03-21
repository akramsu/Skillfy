import streamlit as st
from resume_analyzer.interview_questions import InterviewQuestionsGenerator
import os

def apply_custom_css():
    st.markdown("""
        <style>
        .stButton > button {
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            padding: 0.5rem 2rem;
            border: none;
            width: 100%;
        }
        .stButton > button:hover {
            background-color: #0056b3;
        }
        .form-container {
            background: white;
            padding: 1rem;
            border-radius: 8px;
        }
        h1, h2, h3 {
            color: #2c3e50;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .upload-text {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .stApp {
            background: transparent;
        }
        header {
            display: none;
        }
        footer {
            display: none;
        }
        </style>
    """, unsafe_allow_html=True)

st.set_page_config(page_title='Interview Questions', layout="centered")

# Apply custom CSS
apply_custom_css()

# Form inputs in a single column layout
st.markdown("### Generate Interview Questions")

job_title = st.text_input("Target Job Title:", key="job_title")
uploaded_file = st.file_uploader("Upload Your CV", type=["pdf", "docx"], help="Upload your CV in PDF or DOCX format")

if st.button("Generate Questions", key="generate"):
    if job_title and uploaded_file:
        # Get API key from environment
        gemini_api_key = os.getenv("GEMINI_API_KEY")
        
        # Generate interview questions
        InterviewQuestionsGenerator.generate_interview_questions(gemini_api_key)
    else:
        st.warning("Please fill in all fields before generating questions.")
