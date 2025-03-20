# Skillfy AI - منصة تطوير المهارات والتوجيه المهني بالذكاء الاصطناعي

<div align="center">
  <h3>🌟 مشاركة في هاكاثون سلام 🌟</h3>
</div>

## 🇸🇦 نبذة عن المشروع

منصة مبتكرة تعتمد على الذكاء الاصطناعي التوليدي، تهدف إلى مساعدة المستخدمين في تطوير مهاراتهم الشخصية والمهنية، وتحسين فرصهم الوظيفية. يقدم التطبيق مجموعة من الخدمات المتكاملة:

- **تحليل السيرة الذاتية**: تقييم شامل للسيرة الذاتية مع تحديد نقاط القوة والضعف
- **اقتراح الوظائف المناسبة**: تحديد المسميات الوظيفية الأنسب بناءً على المهارات والخبرات
- **توصيات التعلم**: اقتراح دورات ومصادر تعليمية مخصصة لتطوير المهارات المطلوبة
- **إنشاء سيرة ذاتية احترافية**: مساعدة في إنشاء سيرة ذاتية متميزة
- **إنشاء موقع شخصي**: تصميم وإنشاء موقع شخصي احترافي لعرض المهارات والإنجازات
- **استعراض فرص العمل**: جمع وعرض فرص العمل المناسبة من منصة لينكدإن
- **التحضير للمقابلات**: توليد أسئلة المقابلات المتوقعة والإجابات المثالية

كل ذلك يتم بشكل مخصص وسهل الاستخدام، مما يساعد المستخدمين على تحقيق أهدافهم المهنية بكفاءة أكبر.

## 🇬🇧 Project Overview

An innovative platform powered by generative AI, designed to help users develop their personal and professional skills while enhancing their career opportunities. The application offers a comprehensive suite of integrated services:

- **Resume Analysis**: Comprehensive evaluation of resumes with strengths and weaknesses identification
- **Job Title Suggestions**: Identifying the most suitable job titles based on skills and experience
- **Learning Recommendations**: Suggesting customized courses and educational resources to develop required skills
- **Professional Resume Creation**: Assistance in creating outstanding resumes
- **Personal Website Creation**: Designing and building a professional personal website to showcase skills and achievements
- **Job Opportunity Exploration**: Collecting and displaying suitable job opportunities from LinkedIn
- **Interview Preparation**: Generating expected interview questions and ideal answers

All of this is done in a personalized and user-friendly manner, helping users achieve their career goals more efficiently.

## ⚙️ التقنيات الرئيسية | Key Technologies

- Python
- Streamlit
- Google Generative AI (Gemini)
- LangChain
- PyPDF2
- Selenium
- FAISS (Facebook AI Similarity Search)

## 🚀 المميزات | Features

### 📊 تحليل السيرة الذاتية | Resume Analysis
- **الملخص**: استعراض شامل للمؤهلات والخبرات والمهارات الرئيسية
- **نقاط القوة**: تحديد وتحليل نقاط القوة في السيرة الذاتية
- **نقاط الضعف**: تحديد مجالات التحسين مع اقتراحات عملية
- **تحليل شامل للسيرة الذاتية**: تقييم متكامل للسيرة الذاتية

### 💼 اقتراحات الوظائف | Job Suggestions
- **المسميات الوظيفية المناسبة**: اقتراح وظائف تتناسب مع مهاراتك وخبراتك
- **استكشاف فرص العمل على لينكدإن**: البحث الآلي عن الوظائف المناسبة

### 📚 توصيات التعلم | Learning Recommendations
- **دورات مخصصة**: اقتراح دورات تدريبية بناءً على تحليل السيرة الذاتية
- **مصادر تعليمية متنوعة**: توفير روابط لمنصات تعليمية متعددة

### 📝 إنشاء السيرة الذاتية | Resume Creation
- **قوالب احترافية**: إنشاء سيرة ذاتية بتنسيق احترافي
- **محتوى مخصص**: صياغة محتوى يبرز مهاراتك وخبراتك

### 📄 إنشاء موقع شخصي | Personal Website Creation
- **تصميم احترافي**: تصميم موقع شخصي احترافي لعرض المهارات والإنجازات
- **بناء الموقع**: بناء الموقع الشخصي باستخدام تقنيات الويب الحديثة

### 🎯 التحضير للمقابلات | Interview Preparation
- **أسئلة المقابلات**: توليد أسئلة متوقعة بناءً على الوظيفة والمهارات
- **إجابات مقترحة**: تقديم إرشادات للإجابة على الأسئلة بشكل فعال

## ⚙️ التثبيت | Installation

لتشغيل هذا المشروع، تحتاج إلى تثبيت الحزم التالية:

To run this project, you need to install the following packages:

```bash
pip install -r requirements.txt
```

أو تثبيت كل حزمة على حدة:

Or install each package separately:

```python
pip install streamlit
pip install streamlit_option_menu
pip install streamlit_extras
pip install PyPDF2
pip install langchain
pip install langchain_google_genai
pip install google-generativeai
pip install faiss-cpu
pip install selenium
pip install python-dotenv
```

## 🔑 متطلبات API | API Requirements

يتطلب المشروع مفتاح API من Google Gemini:

The project requires an API key from Google Gemini:

1. احصل على مفتاح API من [Google AI Studio](https://makersuite.google.com/app/apikey)
2. قم بإنشاء ملف `.env` في المجلد الرئيسي وأضف المفتاح:

1. Get an API key from [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create a `.env` file in the root directory and add your key:

```
GEMINI_API_KEY=your_api_key_here
```

## 🚀 الاستخدام | Usage

لتشغيل التطبيق، اتبع الخطوات التالية:

To use this project, follow these steps:

1. استنساخ المستودع: | Clone the repository:
   ```bash
   git clone https://github.com/akramsu/Skillfy_AI.git
   ```

2. الانتقال إلى المجلد: | Navigate to the directory:
   ```bash
   cd Skillfy_AI
   ```

3. تثبيت المتطلبات: | Install requirements:
   ```bash
   pip install -r requirements.txt
   ```

4. تشغيل التطبيق: | Run the app:
   ```bash
   streamlit run src/main.py
   ```

5. الوصول إلى التطبيق في المتصفح على: | Access the app in your browser at:
   ```
   http://localhost:8501
   ```

## 👥 المساهمة | Contributing

نرحب بمساهماتكم! يرجى إرسال طلبات السحب أو فتح مشكلة لاقتراح تحسينات.

Contributions are welcome! Please send pull requests or open an issue to suggest improvements.

## 📄 الترخيص | License

هذا المشروع مرخص بموجب [MIT License](LICENSE).

This project is licensed under the [MIT License](LICENSE).

## 📞 التواصل | Contact

للاستفسارات أو الاقتراحات، يرجى التواصل عبر [GitHub](https://github.com/akramsu).

For inquiries or suggestions, please contact via [GitHub](https://github.com/akramsu).
