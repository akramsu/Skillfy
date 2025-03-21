#  Skillfy AI 

An innovative platform powered by generative AI, designed to help users develop their personal and professional skills while enhancing their career opportunities.

 The application offers a comprehensive suite of integrated services:

- **Resume Analysis**: Comprehensive evaluation of resumes with strengths and weaknesses identification
- **Job Title Suggestions**: Identifying the most suitable job titles based on skills and experience
- **Course Recommendations**: Get personalized course recommendations based on your skills and career goals
- **Learning Recommendations**: Suggesting customized courses and educational resources to develop required skills
- **Professional Resume Creation**: Assistance in creating outstanding resumes
- **Personal Website Creation**: Designing and building a professional personal website to showcase skills and achievements
- **Job Opportunity Exploration**: Collecting and displaying suitable job opportunities from LinkedIn
- **Interview Preparation**: Generating expected interview questions and ideal answers

All of this is done in a personalized and user-friendly manner, helping users achieve their career goals more efficiently.


## Tech Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: Flask, Streamlit
- **AI/ML**: Google Gemini AI
- **Web Scraping**: Selenium
- **Data Processing**: Pandas, NumPy
- **Document Processing**: PyPDF2, python-docx

## Prerequisites

- Python 3.8+
- Streamlit
- XAMPP (for hosting the main website)
- Chrome/Chromium browser (for Selenium)
- Google Generative AI (Gemini)
- FAISS (Facebook AI Similarity Search)
- LangChain
- PyPDF2
- Selenium

## Installation

1. **Clone the repository**

```bash
git clone [https://github.com/gopiashokan/AI-Resume-Analyzer-and-LinkedIn-Scraper-using-Generative-AI](https://github.com/akramsu/Skillfy_AI.git
cd Skillfy_AI
```

2. **Set up XAMPP**

- Install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
- Start Apache server in XAMPP control panel

3. **Create a virtual environment (optional but recommended)**

```bash
python -m venv venv
# On Windows
venv\Scripts\activate
# On macOS/Linux
source venv/bin/activate
```

4. **Install Python dependencies**

```bash
pip install -r requirements.txt
```

5. **Install system dependencies**

```bash
# For Linux/macOS
apt-get install chromium-driver

# For Windows
# Download ChromeDriver from https://chromedriver.chromium.org/downloads
# and add it to your PATH
```

6. **Set up environment variables**

Create a `.env` file in the root directory with the following content:

```
GEMINI_API_KEY=your_gemini_api_key
```

Replace `your_gemini_api_key` with your actual Google Gemini API key. You can obtain one from [Google AI Studio](https://makersuite.google.com/).

## Running the Application

1. **Start the application**

```bash
python start.py
```

This will:
- Start the Flask web server on port 5000
- Launch the Streamlit application on port 8501
- Open your default web browser to the home page

2. **Access the application**

- Main website: [http://localhost:5000](http://localhost:5000)
- Streamlit app: [http://localhost:8501](http://localhost:8501)

## Navigation

The main website and Streamlit application are integrated. You can navigate between them using:

- Links in the main website navigation menu
- "Back to Home" buttons in the Streamlit application

## Project Structure

- `Skillfy_AI/`: Main website files (HTML, CSS, JS)
- `src/`: Python source code
  - `config/`: Configuration files
  - `main.py`: Streamlit application entry point
  - `web_app.py`: Flask application for the main website
- `start.py`: Script to start all components
- `requirements.txt`: Python dependencies

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the LICENSE.md file for details.
