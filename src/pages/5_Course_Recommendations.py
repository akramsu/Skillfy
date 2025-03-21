import streamlit as st
from resume_analyzer.course_recommender import CourseRecommender
import os

st.markdown(f'<h1 style="text-align: center;">Course Recommendations</h1>', unsafe_allow_html=True)

# Get API key from environment
gemini_api_key = os.getenv("GEMINI_API_KEY")

# Get Course Recommendations
CourseRecommender.recommend_courses(gemini_api_key)
