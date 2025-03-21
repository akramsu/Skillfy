// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get form element
    const interviewPrepForm = document.getElementById('interviewPrepForm');
    
    // Add submit event listener to the form
    interviewPrepForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = document.querySelector('.submit-btn');
        const originalBtnText = submitBtn.textContent;
        submitBtn.textContent = 'جاري التحضير...';
        submitBtn.disabled = true;
        
        // Simulate API call with setTimeout
        setTimeout(function() {
            // Get form values
            const jobTitle = document.getElementById('jobTitle').value.trim();
            const industry = document.getElementById('industry').value;
            const experience = document.getElementById('experience').value;
            const companyName = document.getElementById('companyName').value.trim();
            const keySkills = document.getElementById('keySkills').value.trim();
            
            // Generate interview questions based on form values
            generateInterviewQuestions(jobTitle, industry, experience, companyName, keySkills);
            
            // Reset button state
            submitBtn.textContent = originalBtnText;
            submitBtn.disabled = false;
            
            // Show results sections
            document.getElementById('interviewQuestions').style.display = 'block';
            document.getElementById('mockInterview').style.display = 'block';
            
            // Update simulator job title
            document.getElementById('simulatorJobTitle').textContent = jobTitle;
            
            // Scroll to results
            document.getElementById('interviewQuestions').scrollIntoView({ behavior: 'smooth' });
        }, 2000);
    });
    
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Show corresponding content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Mock interview simulator functionality
    const startSimulatorBtn = document.getElementById('startSimulator');
    const pauseSimulatorBtn = document.getElementById('pauseSimulator');
    const resetSimulatorBtn = document.getElementById('resetSimulator');
    const answerInput = document.getElementById('answerInput');
    const submitAnswerBtn = document.getElementById('submitAnswer');
    const answerFeedback = document.getElementById('answerFeedback');
    const nextQuestionBtn = document.getElementById('nextQuestion');
    
    let timerInterval;
    let remainingTime = 120; // 2 minutes in seconds
    let currentQuestionIndex = 0;
    let simulatorQuestions = [];
    
    // Start simulator
    startSimulatorBtn.addEventListener('click', function() {
        // Disable start button and enable pause button
        startSimulatorBtn.disabled = true;
        pauseSimulatorBtn.disabled = false;
        resetSimulatorBtn.disabled = false;
        
        // Enable answer input and submit button
        answerInput.disabled = false;
        submitAnswerBtn.disabled = false;
        
        // Hide feedback section if visible
        answerFeedback.style.display = 'none';
        
        // Get job title for simulator questions
        const jobTitle = document.getElementById('simulatorJobTitle').textContent;
        
        // Generate simulator questions if not already generated
        if (simulatorQuestions.length === 0) {
            simulatorQuestions = generateSimulatorQuestions(jobTitle);
        }
        
        // Display first question
        displaySimulatorQuestion(currentQuestionIndex);
        
        // Start timer
        startTimer();
    });
    
    // Pause simulator
    pauseSimulatorBtn.addEventListener('click', function() {
        if (pauseSimulatorBtn.textContent === 'إيقاف مؤقت') {
            // Pause timer
            clearInterval(timerInterval);
            
            // Update button text
            pauseSimulatorBtn.textContent = 'استئناف';
            
            // Disable answer input and submit button
            answerInput.disabled = true;
            submitAnswerBtn.disabled = true;
        } else {
            // Resume timer
            startTimer();
            
            // Update button text
            pauseSimulatorBtn.textContent = 'إيقاف مؤقت';
            
            // Enable answer input and submit button
            answerInput.disabled = false;
            submitAnswerBtn.disabled = false;
        }
    });
    
    // Reset simulator
    resetSimulatorBtn.addEventListener('click', function() {
        // Stop timer
        clearInterval(timerInterval);
        
        // Reset time
        remainingTime = 120;
        updateTimerDisplay();
        
        // Reset question index
        currentQuestionIndex = 0;
        
        // Reset buttons
        startSimulatorBtn.disabled = false;
        pauseSimulatorBtn.disabled = true;
        pauseSimulatorBtn.textContent = 'إيقاف مؤقت';
        resetSimulatorBtn.disabled = true;
        
        // Reset answer input
        answerInput.value = '';
        answerInput.disabled = true;
        submitAnswerBtn.disabled = true;
        
        // Hide feedback section
        answerFeedback.style.display = 'none';
        
        // Reset current question
        document.getElementById('currentQuestion').textContent = 'اضغط على زر "ابدأ المحاكاة" للبدء.';
    });
    
    // Submit answer
    submitAnswerBtn.addEventListener('click', function() {
        // Stop timer
        clearInterval(timerInterval);
        
        // Get answer
        const answer = answerInput.value.trim();
        
        // Validate answer
        if (answer === '') {
            alert('الرجاء كتابة إجابتك قبل التقديم.');
            startTimer(); // Resume timer
            return;
        }
        
        // Generate feedback
        generateAnswerFeedback(simulatorQuestions[currentQuestionIndex], answer);
        
        // Show feedback section
        answerFeedback.style.display = 'block';
        
        // Disable answer input and submit button
        answerInput.disabled = true;
        submitAnswerBtn.disabled = true;
        pauseSimulatorBtn.disabled = true;
    });
    
    // Next question
    nextQuestionBtn.addEventListener('click', function() {
        // Increment question index
        currentQuestionIndex++;
        
        // Check if we've reached the end of questions
        if (currentQuestionIndex >= simulatorQuestions.length) {
            // Reset to first question
            currentQuestionIndex = 0;
        }
        
        // Display next question
        displaySimulatorQuestion(currentQuestionIndex);
        
        // Reset answer input
        answerInput.value = '';
        answerInput.disabled = false;
        
        // Reset timer
        remainingTime = 120;
        updateTimerDisplay();
        
        // Start timer
        startTimer();
        
        // Hide feedback section
        answerFeedback.style.display = 'none';
        
        // Enable submit button and pause button
        submitAnswerBtn.disabled = false;
        pauseSimulatorBtn.disabled = false;
    });
    
    // Function to start timer
    function startTimer() {
        // Clear any existing interval
        clearInterval(timerInterval);
        
        // Start new interval
        timerInterval = setInterval(function() {
            // Decrement time
            remainingTime--;
            
            // Update timer display
            updateTimerDisplay();
            
            // Check if time is up
            if (remainingTime <= 0) {
                // Stop timer
                clearInterval(timerInterval);
                
                // Automatically submit answer
                submitAnswerBtn.click();
            }
        }, 1000);
    }
    
    // Function to update timer display
    function updateTimerDisplay() {
        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;
        
        // Format time as MM:SS
        const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Update timer element
        document.getElementById('answerTimer').textContent = formattedTime;
        
        // Change color based on remaining time
        const timerElement = document.getElementById('answerTimer');
        if (remainingTime <= 30) {
            timerElement.style.color = '#ff3860';
        } else {
            timerElement.style.color = '#3a86ff';
        }
    }
    
    // Function to display simulator question
    function displaySimulatorQuestion(index) {
        const question = simulatorQuestions[index];
        document.getElementById('currentQuestion').textContent = question.text;
    }
    
    // Add click event listeners to question items (will be created dynamically)
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.question-item')) {
            const questionItem = e.target.closest('.question-item');
            questionItem.classList.toggle('expanded');
        }
    });
});

// Function to generate interview questions based on form values
function generateInterviewQuestions(jobTitle, industry, experience, companyName, keySkills) {
    // Parse key skills into an array
    const skills = keySkills ? keySkills.split(',').map(skill => skill.trim()) : [];
    
    // Generate questions for each category
    const technicalQuestions = generateTechnicalQuestions(jobTitle, industry, experience, skills);
    const behavioralQuestions = generateBehavioralQuestions(experience);
    const companyQuestions = generateCompanyQuestions(companyName, industry);
    const generalQuestions = generateGeneralQuestions();
    
    // Populate question lists
    populateQuestionList('technicalQuestions', technicalQuestions);
    populateQuestionList('behavioralQuestions', behavioralQuestions);
    populateQuestionList('companyQuestions', companyQuestions);
    populateQuestionList('generalQuestions', generalQuestions);
}

// Function to populate a question list
function populateQuestionList(listId, questions) {
    const list = document.getElementById(listId);
    list.innerHTML = '';
    
    questions.forEach(question => {
        const questionItem = document.createElement('div');
        questionItem.className = 'question-item';
        
        questionItem.innerHTML = `
            <h4>${question.text} <i class="fas fa-chevron-down"></i></h4>
            <div class="question-content">
                <p>${question.description}</p>
                <div class="tips">
                    <h5>نصائح للإجابة:</h5>
                    <ul>
                        ${question.tips.map(tip => `<li>${tip}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
        
        list.appendChild(questionItem);
    });
}
