import streamlit as st
from streamlit_extras.add_vertical_space import add_vertical_space
import os
from .resume_analyzer import ResumeAnalyzer

class CourseRecommender:
    PLATFORM_URLS = {
        "Coursera": "https://www.coursera.org",
        "edX": "https://www.edx.org",
        "Udemy": "https://www.udemy.com",
        "LinkedIn Learning": "https://www.linkedin.com/learning",
        "Pluralsight": "https://www.pluralsight.com",
        "DataCamp": "https://www.datacamp.com",
        "Codecademy": "https://www.codecademy.com",
        "freeCodeCamp": "https://www.freecodecamp.org",
        "Google Digital Garage": "https://learndigital.withgoogle.com",
        "Microsoft Learn": "https://learn.microsoft.com",
        "AWS Training": "https://aws.amazon.com/training",
        "IBM Skills": "https://www.ibm.com/training"
    }

    @staticmethod
    def course_recommendations_prompt(query_with_chunks):
        platforms_list = "\n".join([f"           - [{name}]({url})" for name, url in CourseRecommender.PLATFORM_URLS.items()])
        
        return f'''Based on the resume content, recommend online courses to enhance the candidate's skills and career prospects.
        
        Follow these rules:
        1. Focus on skills that would complement their existing expertise
        2. Include a mix of technical and soft skills courses
        3. Include courses from these platforms:
{platforms_list}
           
        4. For each course, provide:
           - Course title (as a clickable link)
           - Platform name (as a clickable link to platform homepage)
           - Brief description (1-2 sentences)
           - Skill level (Beginner/Intermediate/Advanced)
           - Estimated duration
           - Whether it's free or paid
           - Any certification offered
        
        Format your response in markdown with the following sections:
        1. **Technical Skills Courses**
           - Include programming, data, cloud, or other technical courses
           - Mix of free and paid options
           - Format each course as: "[Course Title](course_url) | [Platform](platform_url)"
           
        2. **Professional Development Courses**
           - Include soft skills, leadership, and project management
           - Focus on career advancement skills
           - Format each course as: "[Course Title](course_url) | [Platform](platform_url)"
           
        3. **Industry-Specific Courses**
           - Specialized courses for their industry
           - Include relevant certifications
           - Format each course as: "[Course Title](course_url) | [Platform](platform_url)"
           
        4. **Free Learning Resources**
           - List high-quality free resources with direct links
           - Include tutorials, documentation, and community resources
           - Format each resource as: "[Resource Name](resource_url)"
        
        Resume Content:
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        {query_with_chunks}
        """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
        '''

    @staticmethod
    def recommend_courses(gemini_api_key):
        st.markdown(f'<h4 style="text-align: center;">Get personalized course recommendations based on your resume</h4>', 
                  unsafe_allow_html=True)
        add_vertical_space(1)
        
        with st.form(key='CourseRecommendations'):
            add_vertical_space(1)
            pdf = st.file_uploader(label='Upload Your Resume', type='pdf')
            add_vertical_space(2)
            submit = st.form_submit_button(label='Get Course Recommendations')
            add_vertical_space(1)
        
        add_vertical_space(2)
        if submit:
            if pdf is not None and gemini_api_key != '':
                try:
                    with st.spinner('Analyzing your resume and finding relevant courses...'):
                        pdf_chunks = ResumeAnalyzer.pdf_to_chunks(pdf)
                        recommendations_prompt = CourseRecommender.course_recommendations_prompt(query_with_chunks=pdf_chunks)
                        recommendations = ResumeAnalyzer.gemini(
                            gemini_api_key=gemini_api_key,
                            chunks=pdf_chunks,
                            analyze=recommendations_prompt
                        )

                    st.markdown(f'<h3 style="color: #1E88E5; border-bottom: 2px solid #1E88E5; padding-bottom: 8px;">Recommended Learning Path</h3>', 
                              unsafe_allow_html=True)
                    
                    # Display platform links
                    st.markdown("### 🎓 Learning Platforms")
                    cols = st.columns(3)
                    for i, (name, url) in enumerate(CourseRecommender.PLATFORM_URLS.items()):
                        cols[i % 3].markdown(f"- [{name}]({url})")
                    
                    add_vertical_space(2)
                    st.markdown("### 📚 Recommended Courses")
                    st.markdown(recommendations, unsafe_allow_html=True)
                    
                    st.info("💡 Pro Tips:\n"
                           "- Many platforms offer free trials\n"
                           "- Look for course bundles or subscriptions for better value\n"
                           "- Check if your employer offers learning stipends\n"
                           "- Join course-specific Discord/Slack communities")

                except Exception as e:
                    st.markdown(f'<h5 style="text-align: center;color: orange;">{e}</h5>', 
                              unsafe_allow_html=True)

            elif pdf is None:
                st.markdown(f'<h5 style="text-align: center;color: orange;">Please Upload Your Resume</h5>', 
                          unsafe_allow_html=True)
            
            elif gemini_api_key == '':
                st.markdown(f'<h5 style="text-align: center;color: orange;">Gemini API Key not found in environment variables</h5>', 
                          unsafe_allow_html=True)
