import requests
from typing import List, Dict, Optional
from dataclasses import dataclass
import google.generativeai as genai
import os
from linkedin_scraper.linkedin_scraper import LinkedInScraper

@dataclass
class Job:
    title: str
    company: str
    location: str
    job_type: str
    experience_level: str
    description: str
    url: str
    requirements: List[str]
    benefits: List[str]

class JobSearch:
    def __init__(self):
        self.base_url = "https://www.linkedin.com/jobs/search"
        self.headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
        }
        # Initialize Gemini AI
        api_key = os.getenv('GEMINI_API_KEY')
        if api_key:
            genai.configure(api_key=api_key)
            self.model = genai.GenerativeModel('gemini-1.5-pro-latest')
        else:
            self.model = None
        
        # Initialize LinkedIn scraper
        self.linkedin_scraper = LinkedInScraper()

    def search_jobs_by_cv(self, cv_text: str) -> List[Job]:
        """
        Search for jobs based on CV content using AI analysis
        """
        if not self.model:
            return self._get_sample_jobs()  # Fallback to sample jobs if no API key

        # Use Gemini to analyze CV and extract key skills and experience
        prompt = f"""
        Analyze this CV and extract key information for job matching:
        {cv_text}
        
        Return only the key skills, experience level, and potential job titles that match this candidate's profile.
        """
        
        try:
            response = self.model.generate_content(prompt)
            analysis = response.text
            
            # Extract job titles from analysis
            job_title = self._extract_job_title_from_analysis(analysis)
            
            # Try to use LinkedIn scraper to get real jobs
            try:
                linkedin_jobs = self.linkedin_scraper.search_jobs(job_title=job_title)
                if linkedin_jobs:
                    return [self._convert_to_job_object(job) for job in linkedin_jobs]
            except Exception as e:
                print(f"LinkedIn scraper error: {str(e)}")
            
            # Fallback to sample jobs with filtering
            sample_jobs = self._get_sample_jobs()
            filtered_jobs = []
            
            for job in sample_jobs:
                # Simple matching based on extracted information
                if any(skill.lower() in job.description.lower() for skill in analysis.lower().split()):
                    filtered_jobs.append(job)
            
            return filtered_jobs if filtered_jobs else sample_jobs[:2]  # Return at least 2 sample jobs
        except Exception as e:
            print(f"Error analyzing CV: {str(e)}")
            return self._get_sample_jobs()[:2]  # Fallback to sample jobs

    def _extract_job_title_from_analysis(self, analysis: str) -> str:
        """Extract the most relevant job title from AI analysis"""
        common_titles = [
            "software engineer", "data scientist", "product manager", 
            "frontend developer", "backend developer", "full stack developer",
            "ux designer", "ui designer", "data analyst", "project manager"
        ]
        
        for title in common_titles:
            if title.lower() in analysis.lower():
                return title
        
        # Default to a generic title if none found
        return "software engineer"

    def _convert_to_job_object(self, job_dict: Dict) -> Job:
        """Convert a dictionary to a Job object"""
        return Job(
            title=job_dict.get("title", ""),
            company=job_dict.get("company", ""),
            location=job_dict.get("location", ""),
            job_type=job_dict.get("job_type", ""),
            experience_level=job_dict.get("experience_level", ""),
            description=job_dict.get("description", ""),
            url=job_dict.get("url", ""),
            requirements=job_dict.get("requirements", []),
            benefits=job_dict.get("benefits", [])
        )

    def search_jobs(
        self,
        job_title: str,
        location: Optional[str] = None,
        job_type: Optional[str] = None,
        experience_level: Optional[str] = None
    ) -> List[Job]:
        """
        Search for jobs on LinkedIn based on given criteria
        """
        # Try to use LinkedIn scraper to get real jobs
        try:
            linkedin_jobs = self.linkedin_scraper.search_jobs(
                job_title=job_title,
                location=location,
                job_type=job_type,
                experience_level=experience_level
            )
            
            if linkedin_jobs:
                return [self._convert_to_job_object(job) for job in linkedin_jobs]
        except Exception as e:
            print(f"LinkedIn scraper error: {str(e)}")
        
        # Fallback to sample jobs
        sample_jobs = self._get_sample_jobs()

        # Filter jobs based on criteria
        filtered_jobs = []
        for job in sample_jobs:
            if (not location or location.lower() in job.location.lower()) and \
               (not job_type or job_type.lower() in job.job_type.lower()) and \
               (not experience_level or experience_level.lower() in job.experience_level.lower()):
                filtered_jobs.append(job)

        return filtered_jobs

    def _get_sample_jobs(self) -> List[Job]:
        """
        Return sample jobs data
        """
        return [
            Job(
                title="Software Engineer",
                company="Tech Corp",
                location="San Francisco, CA",
                job_type="Full-time",
                experience_level="Mid-Senior level",
                description="We are looking for a skilled Software Engineer to join our dynamic team. You will be responsible for developing high-quality software solutions, collaborating with cross-functional teams, and contributing to the full software development lifecycle.",
                url="https://www.linkedin.com/jobs/view/123",
                requirements=[
                    "5+ years of experience in software development",
                    "Strong knowledge of Python",
                    "Experience with web frameworks"
                ],
                benefits=[
                    "Competitive salary",
                    "Health insurance",
                    "Remote work options"
                ]
            ),
            Job(
                title="Data Scientist",
                company="Data Analytics Inc",
                location="New York, NY",
                job_type="Full-time",
                experience_level="Entry level",
                description="Join our data science team to help build and optimize our data analytics platform. You'll work on challenging problems, develop machine learning models, and create data-driven solutions for our clients.",
                url="https://www.linkedin.com/jobs/view/456",
                requirements=[
                    "Bachelor's degree in Computer Science or related field",
                    "Experience with machine learning frameworks",
                    "Strong understanding of data structures and algorithms"
                ],
                benefits=[
                    "Opportunity to work with large datasets",
                    "Collaborative team environment",
                    "Professional development opportunities"
                ]
            ),
            Job(
                title="Frontend Developer",
                company="Web Solutions Ltd",
                location="Austin, TX",
                job_type="Contract",
                experience_level="Mid-Senior level",
                description="Create beautiful and responsive web applications using modern frontend technologies. Experience with React, TypeScript, and modern web development practices required.",
                url="https://www.linkedin.com/jobs/view/101",
                requirements=[
                    "3+ years of frontend development experience",
                    "Proficiency in React and TypeScript",
                    "Experience with modern web technologies"
                ],
                benefits=[
                    "Flexible work hours",
                    "Project completion bonuses",
                    "Remote work options"
                ]
            )
        ]

    def get_job_details(self, job_id: str) -> Optional[Dict]:
        """
        Get detailed information about a specific job
        """
        # This would typically make an API call to LinkedIn
        # For demo purposes, return sample data
        return {
            "title": "Software Engineer",
            "company": "Tech Corp",
            "location": "San Francisco, CA",
            "description": "Detailed job description...",
            "requirements": [
                "5+ years of experience in software development",
                "Strong knowledge of Python",
                "Experience with web frameworks"
            ],
            "benefits": [
                "Competitive salary",
                "Health insurance",
                "Remote work options"
            ]
        }
