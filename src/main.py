import streamlit as st
from dotenv import load_dotenv
import warnings
import google.generativeai as genai
from config.config import GEMINI_API_KEY
import ssl

# Load environment variables
load_dotenv()

warnings.filterwarnings('ignore')

# SSL Configuration
ssl._create_default_https_context = ssl._create_unverified_context

# Configure Gemini API globally with explicit credentials
if GEMINI_API_KEY:
    genai.configure(api_key=GEMINI_API_KEY)

def main():
    # page configuration
    st.set_page_config(page_title='Resume Analyzer AI', layout="wide")

   

    # page header transparent color
    page_background_color = """
    <style>
    [data-testid="stHeader"] 
    {
        background: rgba(0,0,0,0);
    }
    </style>
    """
    st.markdown(page_background_color, unsafe_allow_html=True)

    # title and position
    st.markdown(f'<h1 style="text-align: center;">Resume Analyzer AI</h1>',
                unsafe_allow_html=True)

if __name__ == "__main__":
    main()
