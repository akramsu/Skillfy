import streamlit as st
from resume_analyzer.cv_analyzer import CVAnalyzer
import os

st.set_page_config(page_title='CV Analysis', layout="wide")

st.markdown(f'<h1 style="text-align: center;">CV Analysis</h1>', unsafe_allow_html=True)

# Get API key from environment
gemini_api_key = os.getenv("GEMINI_API_KEY")

# Run CV Analysis
CVAnalyzer.analyze_cv(gemini_api_key)
