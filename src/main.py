import streamlit as st
from streamlit_option_menu import option_menu
from streamlit_extras.add_vertical_space import add_vertical_space
import os
from dotenv import load_dotenv
import warnings
import google.generativeai as genai
from config.config import GEMINI_API_KEY
from resume_analyzer.resume_analyzer import ResumeAnalyzer
from resume_analyzer.weakness_analyzer import WeaknessAnalyzer
from resume_analyzer.job_titles_analyzer import JobTitlesAnalyzer
from resume_analyzer.interview_questions import InterviewQuestionsGenerator
from resume_analyzer.cv_analyzer import CVAnalyzer
from resume_analyzer.course_recommender import CourseRecommender
from resume_creator.resume_creator import ResumeCreator
from linkedin_scraper.linkedin_scraper import LinkedInScraper
import ssl

# Load environment variables
load_dotenv()

warnings.filterwarnings('ignore')

# SSL Configuration
ssl._create_default_https_context = ssl._create_unverified_context

# Configure Gemini API globally with explicit credentials
if GEMINI_API_KEY:
    genai.configure(api_key=GEMINI_API_KEY)

def streamlit_config():
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

def main():
    streamlit_config()
    
    # Create the menu
    with st.sidebar:
        add_vertical_space(2)
        selected = option_menu(
            menu_title="Menu",
            options=["Summary", "Strength", "Weakness", "Job Titles", "Interview Questions", 
                    "CV Analysis", "Course Recommendations", "Create Resume", "LinkedIn Jobs"],
            icons=["file-earmark-text", "star", "exclamation-triangle", "briefcase", 
                  "chat-dots", "file-earmark-person", "mortarboard", "pencil-square", "linkedin"],
            menu_icon="cast",
            default_index=0,
        )
        add_vertical_space(2)
    
    # Get API key from environment
    gemini_api_key = os.getenv("GEMINI_API_KEY")
    
    # Route to the appropriate feature based on selection
    if selected == "Summary":
        ResumeAnalyzer.resume_summary(gemini_api_key)
    elif selected == "Strength":
        ResumeAnalyzer.analyze_strength(gemini_api_key)
    elif selected == "Weakness":
        WeaknessAnalyzer.analyze_weakness(gemini_api_key)
    elif selected == "Job Titles":
        JobTitlesAnalyzer.suggest_job_titles(gemini_api_key)
    elif selected == "Interview Questions":
        InterviewQuestionsGenerator.generate_interview_questions(gemini_api_key)
    elif selected == "CV Analysis":
        CVAnalyzer.analyze_cv(gemini_api_key)
    elif selected == "Course Recommendations":
        CourseRecommender.recommend_courses(gemini_api_key)
    elif selected == "Create Resume":
        ResumeCreator.create_resume(gemini_api_key)
    elif selected == "LinkedIn Jobs":
        LinkedInScraper.main()

if __name__ == "__main__":
    main()
