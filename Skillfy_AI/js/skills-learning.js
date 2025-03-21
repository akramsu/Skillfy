// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get form element
    const skillsForm = document.getElementById('skillsForm');
    
    // Add submit event listener to the form
    skillsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = document.querySelector('.submit-btn');
        const originalBtnText = submitBtn.textContent;
        submitBtn.textContent = 'جاري التحليل...';
        submitBtn.disabled = true;
        
        // Simulate API call with setTimeout
        setTimeout(function() {
            // Get form values
            const jobTitle = document.getElementById('jobTitle').value.trim();
            const industry = document.getElementById('industry').value;
            const experience = document.getElementById('experience').value;
            const location = document.getElementById('location').value.trim();
            const currentSkills = document.getElementById('currentSkills').value.trim();
            
            // Generate results based on form values
            generateSkillsResults(jobTitle, industry, experience, location, currentSkills);
            
            // Reset button state
            submitBtn.textContent = originalBtnText;
            submitBtn.disabled = false;
            
            // Show results sections
            document.getElementById('skillsResults').style.display = 'block';
            document.getElementById('learningResources').style.display = 'block';
            
            // Scroll to results
            document.getElementById('skillsResults').scrollIntoView({ behavior: 'smooth' });
        }, 2000);
    });
});

// Function to generate skills results based on form inputs
function generateSkillsResults(jobTitle, industry, experience, location, currentSkills) {
    // Parse current skills into an array
    const userSkills = currentSkills ? currentSkills.split(',').map(skill => skill.trim()) : [];
    
    // Generate skills based on job title and industry
    let technicalSkills = [];
    let softSkills = [];
    let certifications = [];
    let skillsGap = [];
    
    // Define skills based on job title
    if (jobTitle.includes('مطور') || jobTitle.includes('برمجيات') || jobTitle.includes('مهندس')) {
        if (jobTitle.includes('ويب') || jobTitle.includes('واجهة') || jobTitle.includes('frontend') || jobTitle.includes('front-end')) {
            technicalSkills = [
                'HTML5 و CSS3',
                'JavaScript (ES6+)',
                'React.js أو Vue.js أو Angular',
                'تصميم واجهات المستخدم (UI/UX)',
                'تطوير تطبيقات الويب التفاعلية',
                'تحسين أداء الواجهة الأمامية',
                'اختبار واجهة المستخدم'
            ];
            
            softSkills = [
                'حل المشكلات',
                'العمل ضمن فريق',
                'التفكير الإبداعي',
                'التواصل الفعال',
                'إدارة الوقت'
            ];
            
            certifications = [
                'شهادة مطور ويب معتمد',
                'شهادة React Developer',
                'شهادة في تصميم واجهات المستخدم'
            ];
            
            // Generate skills gap
            skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
            
            // Generate learning resources
            generateLearningResources(technicalSkills, softSkills, skillsGap);
        } 
        else if (jobTitle.includes('خلفية') || jobTitle.includes('backend') || jobTitle.includes('back-end')) {
            technicalSkills = [
                'لغات البرمجة الخلفية (Node.js, Python, Java, PHP)',
                'تطوير واجهات برمجة التطبيقات (APIs)',
                'قواعد البيانات SQL و NoSQL',
                'أمان تطبيقات الويب',
                'تصميم وتطوير الخدمات المصغرة (Microservices)',
                'إدارة الخوادم والبنية التحتية',
                'Docker و Kubernetes'
            ];
            
            softSkills = [
                'التفكير التحليلي',
                'حل المشكلات المعقدة',
                'العمل ضمن فريق',
                'التواصل التقني',
                'إدارة الوقت'
            ];
            
            certifications = [
                'شهادة مطور Node.js',
                'شهادة AWS أو Azure',
                'شهادة في أمان المعلومات'
            ];
            
            // Generate skills gap
            skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
            
            // Generate learning resources
            generateLearningResources(technicalSkills, softSkills, skillsGap);
        }
        else if (jobTitle.includes('full stack') || jobTitle.includes('فول ستاك')) {
            technicalSkills = [
                'تطوير الواجهة الأمامية (HTML, CSS, JavaScript)',
                'أطر عمل الواجهة الأمامية (React, Angular, Vue)',
                'تطوير الخلفية (Node.js, Python, Java)',
                'قواعد البيانات SQL و NoSQL',
                'تطوير واجهات برمجة التطبيقات (APIs)',
                'أمان تطبيقات الويب',
                'DevOps وتكامل مستمر'
            ];
            
            softSkills = [
                'التفكير الشمولي',
                'حل المشكلات المعقدة',
                'العمل ضمن فريق',
                'التواصل الفعال',
                'إدارة المشاريع',
                'التعلم المستمر'
            ];
            
            certifications = [
                'شهادة مطور Full Stack',
                'شهادة AWS أو Azure',
                'شهادة في تطوير تطبيقات الويب'
            ];
            
            // Generate skills gap
            skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
            
            // Generate learning resources
            generateLearningResources(technicalSkills, softSkills, skillsGap);
        }
        else if (jobTitle.includes('موبايل') || jobTitle.includes('تطبيقات')) {
            technicalSkills = [
                'تطوير تطبيقات Android (Java/Kotlin)',
                'تطوير تطبيقات iOS (Swift)',
                'أطر عمل متعددة المنصات (React Native, Flutter)',
                'تصميم واجهات المستخدم للموبايل',
                'اختبار تطبيقات الموبايل',
                'التكامل مع خدمات الويب والواجهات الخلفية',
                'أمان تطبيقات الموبايل'
            ];
            
            softSkills = [
                'التفكير الإبداعي',
                'حل المشكلات',
                'العمل ضمن فريق',
                'التواصل الفعال',
                'التعلم المستمر'
            ];
            
            certifications = [
                'شهادة مطور Android معتمد',
                'شهادة مطور iOS معتمد',
                'شهادة في تطوير تطبيقات متعددة المنصات'
            ];
            
            // Generate skills gap
            skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
            
            // Generate learning resources
            generateLearningResources(technicalSkills, softSkills, skillsGap);
        }
        else if (jobTitle.includes('بيانات') || jobTitle.includes('data')) {
            technicalSkills = [
                'لغات تحليل البيانات (Python, R)',
                'SQL وقواعد البيانات',
                'تحليل البيانات وتصورها',
                'الإحصاء والرياضيات',
                'أدوات تحليل البيانات (Pandas, NumPy, Tableau)',
                'التعلم الآلي (أساسيات)',
                'معالجة البيانات الكبيرة'
            ];
            
            softSkills = [
                'التفكير التحليلي',
                'حل المشكلات',
                'التواصل الفعال',
                'العرض والتقديم',
                'الفضول والتعلم المستمر'
            ];
            
            certifications = [
                'شهادة محلل بيانات معتمد',
                'شهادة في علوم البيانات',
                'شهادة Tableau أو Power BI'
            ];
            
            // Generate skills gap
            skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
            
            // Generate learning resources
            generateLearningResources(technicalSkills, softSkills, skillsGap);
        }
        else if (jobTitle.includes('ذكاء') || jobTitle.includes('AI') || jobTitle.includes('machine learning')) {
            technicalSkills = [
                'لغات برمجة الذكاء الاصطناعي (Python)',
                'أطر عمل التعلم الآلي (TensorFlow, PyTorch)',
                'معالجة اللغات الطبيعية',
                'رؤية الكمبيوتر',
                'التعلم العميق',
                'تحليل البيانات الكبيرة',
                'الإحصاء والرياضيات المتقدمة'
            ];
            
            softSkills = [
                'التفكير التحليلي',
                'حل المشكلات المعقدة',
                'البحث والتطوير',
                'التواصل التقني',
                'العمل ضمن فريق متعدد التخصصات'
            ];
            
            certifications = [
                'شهادة في علوم البيانات والذكاء الاصطناعي',
                'شهادة TensorFlow Developer',
                'شهادة في التعلم العميق'
            ];
            
            // Generate skills gap
            skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
            
            // Generate learning resources
            generateLearningResources(technicalSkills, softSkills, skillsGap);
        }
    }
    else if (jobTitle.includes('محلل') || jobTitle.includes('analyst')) {
        technicalSkills = [
            'تحليل البيانات',
            'Excel المتقدم',
            'SQL وقواعد البيانات',
            'أدوات تصور البيانات (Tableau, Power BI)',
            'أساسيات البرمجة (Python أو R)',
            'تحليل الأعمال',
            'إعداد التقارير'
        ];
        
        softSkills = [
            'التفكير التحليلي',
            'حل المشكلات',
            'التواصل الفعال',
            'العرض والتقديم',
            'الانتباه للتفاصيل'
        ];
        
        certifications = [
            'شهادة محلل أعمال معتمد',
            'شهادة في تحليل البيانات',
            'شهادة Tableau أو Power BI'
        ];
        
        // Generate skills gap
        skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
        
        // Generate learning resources
        generateLearningResources(technicalSkills, softSkills, skillsGap);
    }
    else if (jobTitle.includes('مدير') || jobTitle.includes('قائد') || jobTitle.includes('manager')) {
        technicalSkills = [
            'إدارة المشاريع',
            'أدوات إدارة المشاريع (JIRA, Asana, Trello)',
            'تخطيط الموارد',
            'إدارة الميزانية',
            'تحليل البيانات الأساسي',
            'إعداد التقارير',
            'استراتيجيات الأعمال'
        ];
        
        softSkills = [
            'القيادة',
            'التواصل الفعال',
            'إدارة الفريق',
            'حل النزاعات',
            'التفكير الاستراتيجي',
            'اتخاذ القرارات',
            'إدارة الوقت'
        ];
        
        certifications = [
            'شهادة PMP في إدارة المشاريع',
            'شهادة في القيادة والإدارة',
            'شهادة Scrum Master'
        ];
        
        // Generate skills gap
        skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
        
        // Generate learning resources
        generateLearningResources(technicalSkills, softSkills, skillsGap);
    }
    else {
        // Default skills for other job titles
        technicalSkills = [
            'مهارات الكمبيوتر الأساسية',
            'برامج Microsoft Office',
            'إدارة البريد الإلكتروني',
            'أدوات التواصل والتعاون عبر الإنترنت',
            'أساسيات تحليل البيانات',
            'إدارة المهام والمشاريع'
        ];
        
        softSkills = [
            'التواصل الفعال',
            'العمل ضمن فريق',
            'حل المشكلات',
            'إدارة الوقت',
            'التفكير النقدي',
            'المرونة والتكيف'
        ];
        
        certifications = [
            'شهادات Microsoft Office',
            'شهادات في مجال العمل المحدد',
            'دورات في مهارات التواصل والقيادة'
        ];
        
        // Generate skills gap
        skillsGap = generateSkillsGap(technicalSkills, softSkills, userSkills);
        
        // Generate learning resources
        generateLearningResources(technicalSkills, softSkills, skillsGap);
    }
    
    // Populate the skills lists
    populateSkillsList('technicalSkills', technicalSkills);
    populateSkillsList('softSkills', softSkills);
    populateSkillsList('certifications', certifications);
    populateSkillsList('skillsGap', skillsGap);
}

// Function to generate skills gap based on user's current skills
function generateSkillsGap(technicalSkills, softSkills, userSkills) {
    // Combine all required skills
    const allRequiredSkills = [...technicalSkills, ...softSkills];
    
    // Filter out skills that the user already has
    return allRequiredSkills.filter(skill => {
        // Check if any user skill is similar to the required skill
        return !userSkills.some(userSkill => 
            skill.toLowerCase().includes(userSkill.toLowerCase()) || 
            userSkill.toLowerCase().includes(skill.toLowerCase())
        );
    }).slice(0, 8); // Limit to 8 skills for better display
}

// Function to populate a skills list
function populateSkillsList(listId, skills) {
    const list = document.getElementById(listId);
    list.innerHTML = '';
    
    skills.forEach(skill => {
        const li = document.createElement('li');
        li.textContent = skill;
        list.appendChild(li);
    });
}

// Function to generate learning resources based on skills gap
function generateLearningResources(technicalSkills, softSkills, skillsGap) {
    const resourcesContainer = document.getElementById('resourcesContainer');
    resourcesContainer.innerHTML = '';
    
    // Sample resource types
    const resourceTypes = ['دورة تدريبية', 'كتاب', 'مقال', 'فيديو تعليمي', 'ندوة عبر الإنترنت'];
    
    // Sample providers
    const providers = ['Coursera', 'Udemy', 'LinkedIn Learning', 'edX', 'Pluralsight', 'منصة إدراك', 'منصة رواق', 'أكاديمية حسوب'];
    
    // Generate resources for skills gap (limited to 6 for better display)
    const limitedSkillsGap = skillsGap.slice(0, 6);
    
    limitedSkillsGap.forEach(skill => {
        // Randomly select resource type and provider
        const resourceType = resourceTypes[Math.floor(Math.random() * resourceTypes.length)];
        const provider = providers[Math.floor(Math.random() * providers.length)];
        
        // Create resource card
        const resourceCard = document.createElement('div');
        resourceCard.className = 'resource-card';
        
        // Generate random image for the resource
        const imageIndex = Math.floor(Math.random() * 3) + 1;
        
        resourceCard.innerHTML = `
            <div class="resource-image">
                <img src="../assets/images/blog${imageIndex}.png" alt="${skill}">
            </div>
            <div class="resource-content">
                <span class="resource-type">${resourceType}</span>
                <h3 class="resource-title">${resourceType === 'كتاب' ? 'كتاب: ' : ''}${skill}</h3>
                <p class="resource-provider">${provider}</p>
                <p class="resource-description">تعلم ${skill} من خلال ${resourceType} مخصص يغطي جميع الجوانب الأساسية والمتقدمة.</p>
                <a href="#" class="resource-link">عرض المصدر</a>
            </div>
        `;
        
        resourcesContainer.appendChild(resourceCard);
    });
    
    // Generate learning path
    generateLearningPath(limitedSkillsGap);
}

// Function to generate learning path
function generateLearningPath(skills) {
    const learningPath = document.getElementById('learningPath');
    learningPath.innerHTML = '';
    
    // Create a timeline of learning steps
    skills.forEach((skill, index) => {
        const pathItem = document.createElement('div');
        pathItem.className = 'path-item';
        
        // Generate random duration (1-4 weeks)
        const duration = Math.floor(Math.random() * 4) + 1;
        
        pathItem.innerHTML = `
            <h4>الخطوة ${index + 1}: ${skill}</h4>
            <p>تعلم أساسيات ومفاهيم متقدمة في ${skill} من خلال مجموعة من الدورات والتطبيقات العملية.</p>
            <div class="duration">
                <i class="fas fa-clock"></i>
                المدة المقترحة: ${duration} ${duration === 1 ? 'أسبوع' : 'أسابيع'}
            </div>
        `;
        
        learningPath.appendChild(pathItem);
    });
}
