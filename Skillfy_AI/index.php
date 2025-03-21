<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Skillfy - AI-Powered Career Guidance</title>
  <link rel="stylesheet" href="styles/index.css">
</head>
<body>
  <nav>
    <div class="container nav-container">
      <a href="#" class="logo">
        <svg class="logo-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
        </svg>
        <span class="logo-text">Skillfy</span>
      </a>
      
      <div class="nav-links">
        <a href="home.html">Home</a>
        <a href="./pages/jobs.php">Jobs</a>
        <a href="#">About Us</a>
        <a href="contact.html">Contact Us</a>
      </div>
      
      <div class="auth-buttons">
        <button class="login-btn" onclick="window.location.href = './pages/login.php';">Login</button>
        <button class="register-btn" onclick="window.location.href = './pages/signup.php';">Register</button>
      </div>
    </div>
  </nav>

  <main class="hero">
    <div class="container hero-container">
      <div class="hero-content">
        <h1 class="hero-title">Discover Your Professional Potential with AI-Powered Guidance</h1>
        <p class="hero-description">
          Skillfy uses advanced AI to help you identify market-demanded skills,
          build a standout resume, and accelerate your career journey
        </p>
      </div>
      
      <div class="hero-image-container">
        <img 
          src="https://images.unsplash.com/photo-1507679799987-c73779587ccf?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1171&q=80" 
          alt="Professional with briefcase" 
          class="hero-image"
        >
        <div class="image-overlay"></div>
      </div>
    </div>


        <div class="section-divider">
            <div class="divider-content">
                <div class="divider-text">
                    Powered by Advanced AI Technology
                </div>
                <div class="icons-container">
                    <div class="icon-item">
                        <div class="icon-square">AI</div>
                    </div>
                    
                    <div class="icon-item">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M12 2L15 8L22 9L17 14L18 21L12 17.5L6 21L7 14L2 9L9 8L12 2Z" fill="white"/>
                        </svg>
                    </div>
                    
                    <div class="icon-item">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                        </svg>
                    </div>
                    
                    <div class="icon-item">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/>
                            <path d="M7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        

  <section class="ai-features">
    <div class="container features-container">
      <div class="features-image-wrapper">
        <img 
          src="./assets/images/ai.png"
          alt="AI Processor Visualization" 
          class="features-image"
        >
        <div class="image-border"></div>
      </div>
      
      <div class="features-content">
        <h2 class="features-title">Empowering Your Career Growth with AI-Driven Insights</h2>
        <p class="features-description">
          Skillfy was developed by a team of AI experts and career development professionals to provide personalized guidance through every step of your career journey. Our AI analyzes current job market trends to help you develop the most in-demand skills
        </p>
        <button class="get-started-btn">Get Started</button>
      </div>
    </div>
  </section>



  <section class="toolkit">
    <div class="container toolkit-container">
      <div class="toolkit-content">
        <h2 class="toolkit-title">Your Complete AI Career Development Toolkit</h2>
        <p class="toolkit-description">
            Unlock the power of AI to supercharge your career journey. Our cutting-edge platform leverages advanced generative models to optimize your resume, match your skills with top job opportunities, and help you stand out in the competitive job market. Whether you're looking to enhance your professional profile or find your dream job, our AI-driven tools are here to guide you every step of the way.
        </p>
        <div class="metrics">
          <div class="metric">
            <div class="progress-ring">
              <svg width="120" height="120" viewBox="0 0 120 120">
                <circle class="progress-ring-circle-bg" cx="60" cy="60" r="54" />
                <circle class="progress-ring-circle" cx="60" cy="60" r="54" stroke-dasharray="339.292" stroke-dashoffset="27.14336" />
              </svg>
              <span class="progress-text">92%</span>
            </div>
            <h3 class="metric-title">Skills Match Accuracy</h3>
          </div>
          <div class="metric">
            <div class="progress-ring">
              <svg width="120" height="120" viewBox="0 0 120 120">
                <circle class="progress-ring-circle-bg" cx="60" cy="60" r="54" />
                <circle class="progress-ring-circle" cx="60" cy="60" r="54" stroke-dasharray="339.292" stroke-dashoffset="44.10796" />
              </svg>
              <span class="progress-text">87%</span>
            </div>
            <h3 class="metric-title">Resume Improvement Rate</h3>
          </div>
        </div>
      </div>
      <div class="toolkit-image-wrapper">
        <img 
          src="./assets/images/resume.png"
          alt="Person writing notes" 
          class="toolkit-image"
        >
        <div class="image-border"></div>
      </div>
    </div>
  </section>



  
  <section class="journey">
    <div class="container journey-container">
      <div class="journey-header">
        <h2 class="journey-title">Your Career<br>Growth<br>Journey</h2>
      </div>
      <div class="journey-steps">
        <div class="journey-step">
          <div class="step-number">01</div>
          <div class="step-content">
            <h3 class="step-title">Skills Discovery</h3>
            <p class="step-description">Our AI analyzes your background</p>
            <div class="step-line"></div>
          </div>
        </div>
        
        <div class="journey-step">
          <div class="step-number">02</div>
          <div class="step-content">
            <h3 class="step-title">Market Analysis</h3>
            <p class="step-description">Explore in-demand skills in your industry with real-time data and future trend projections</p>
            <div class="step-line"></div>
          </div>
        </div>
        
        <div class="journey-step">
          <div class="step-number">03</div>
          <div class="step-content">
            <h3 class="step-title">Personalized Learning</h3>
            <p class="step-description">Access customized learning resources tailored to your specific skill gaps and career goals</p>
            <div class="step-line"></div>
          </div>
        </div>
        
        <div class="journey-step">
          <div class="step-number">04</div>
          <div class="step-content">
            <h3 class="step-title">Profile Optimization</h3>
            <p class="step-description">Transform your resume and online presence to showcase your evolving professional expertise</p>
          </div>
        </div>
      </div>
    </div>
    <div class="journey-decoration"></div>
  </section>


  <section class="services">
    <div class="services-grid">
        <div class="service-item">
            <div class="service-content">
                <h2 class="service-title">Skills Assessment</h2>
                <p class="service-description">
                    Our AI technology analyzes your skills and compares them to market demands, providing a skills gap analysis
                </p>
            </div>
        </div>

        <div class="service-image">
            <img src="./assets/images/resume_builder.png" alt="Resume and laptop on marble surface" class="center-image">
        </div>

        <div class="service-item">
            <div class="service-content">
                <h2 class="service-title">Job Matching</h2>
                <p class="service-description">
                    Discover job opportunities that align with your skills profile and receive personalized application advice
                </p>
            </div>
        </div>

        <div class="service-image center-image">
            <img src="./assets/images/skill_assessment.png" alt="People discussing at desk" class="full-image">
        </div>

        <div class="service-item dark">
            <div class="service-content">
                <h2 class="service-title">Resume Builder</h2>
                <p class="service-description">
                    Create a professional, ATS-optimized resume with our AI-powered tools that highlight your most relevant skills
                </p>
            </div>
        </div>

        <div class="service-image center-image">
            <img src="./assets/images/job_matching.png" alt="We are hiring sign" class="full-image">
        </div>
    </div>
</section>



  
  <section class="transform">
    <div class="container transform-container">
      <h2 class="transform-title">Transform Your Career with Personalized AI Guidance</h2>
      <p class="transform-description">
        Our advanced AI analyzes current job market trends and your unique skills profile to create a personalized career development path. Start with a free assessment today to discover your optimal career trajectory
      </p>
      <button class="assessment-btn">Begin Assessment</button>
    </div>
    <div class="transform-decoration"></div>
  </section>


  
  </main>

  <footer class="footer">
    <div class="footer-content">
        <div class="footer-brand">
            <a class="logo">
                <svg class="logo-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
                <span>Skillfy</span>
            </a>
            <p>Skillfy leverages advanced AI technology to help professionals identify in-demand skills, optimize their resumes, and accelerate their career growth.</p>
        </div>
        
        <div class="footer-column">
            <h3>Company</h3>
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Our Team</a></li>
                <li><a href="#">Partners</a></li>
                <li><a href="#">For Candidates</a></li>
                <li><a href="#">For Employers</a></li>
            </ul>
        </div>
        
        <div class="footer-column">
            <h3>Career Resources</h3>
            <ul>
                <li><a href="#">Skills Library</a></li>
                <li><a href="#">Learning Paths</a></li>
                <li><a href="#">Resume Templates</a></li>
                <li><a href="#">Interview Guides</a></li>
                <li><a href="#">Salary Insights</a></li>
            </ul>
        </div>
        
        <div class="footer-newsletter">
            <h3>Newsletter</h3>
            <p>Stay updated with the latest job market trends and skill demands in your industry</p>
            <div class="form">
                <input type="email" placeholder="Email Address">
                <button>Subscribe now</button>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="footer-copyright">
            &copy; Copyright Skillfy 2025. Designed by Saba Team
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
        </div>
    </div>
</footer>

</body>
</html>