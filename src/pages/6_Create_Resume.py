import streamlit as st
from resume_creator.resume_creator import ResumeCreator
import os

st.markdown(f'<h1 style="text-align: center;">Create Resume</h1>', unsafe_allow_html=True)

# Get API key from environment
gemini_api_key = os.getenv("GEMINI_API_KEY")

# Create Resume
ResumeCreator.create_resume(gemini_api_key)
