import streamlit as st
from resume_analyzer.resume_analyzer import ResumeAnalyzer
from resume_analyzer.weakness_analyzer import WeaknessAnalyzer
import os

st.set_page_config(page_title='Strength & Weakness Analysis', layout="wide")

st.markdown(f'<h1 style="text-align: center;">Strength & Weakness Analysis</h1>', unsafe_allow_html=True)

# Get API key from environment
gemini_api_key = os.getenv("GEMINI_API_KEY")

tab1, tab2 = st.tabs(["Strength Analysis", "Weakness Analysis"])

with tab1:
    st.header("Strength Analysis")
    ResumeAnalyzer.analyze_strength(gemini_api_key)

with tab2:
    st.header("Weakness Analysis")
    WeaknessAnalyzer.analyze_weakness(gemini_api_key)
