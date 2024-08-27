# Welcome to Career Coach AI
**
- career_coach.py (Program)
- app.py (API)
- README.md
**
## Task
CareerCoachAI is designed to assist users in optimizing their resumes, cover letters, and interview preparation by cross-referencing their resume and job description with the help of Generative AI.

## Description
CareerCoachAI provides personalized career coaching through several key functionalities. The project includes the following steps:

1. Call API: call_openai_api(self, prompt_dict, max_tokens) function is designed to send a request to OpenAI in json format. Fields to pay attention to:
    "model="gpt-4", #Specify the model you would like to use. 
    messages=[
        {"role": "system", "content": "I want you to act as an experienced HR manager."}, #Describe the role that you want ChatGPT to perform
        {"role": "user", "content": prompt} #Prompt that will be sent to ChatGPT for processing.
            ],
    max_tokens = max_tokens - As each function's response varies in length, you can select the maximum number of tokens you would like ChatGPT to produce in its response. For example, a two-page resume may require 5000-6000 tokens, but a list if interview questions only a 1000 or less. 


2. Data Loading & Preprocessing: The second step in the CareerCoachAI process is loading and extracting text from the user’s resume and the job description provided. The application supports multiple file formats, including PDF, DOC, and DOCX, ensuring flexibility for users.

-- During text extraction, the application removes unnecessary characters such as newlines (\n) and tabs (\t) to ensure that the text is clean and ready for further processing.
-- The cleaned text is stored within the program to prevent repetative extraction, improving its efficiency.
-- The user is shown an error message if a different format is submitted.

3. Tailor Resume and Cover Letter: 

One of the core functionalities of CareerCoachAI is to tailor the user’s resume to better align with the job description. The AI model analyzes both the resume and job description, making adjustments to ensure that the resume reflects the requirements of the specific job.

Each function looks similar to this code snippet:

   def tailor_resume(self):
        if self.resume_text == "Unsupported file type" or self.job_description == "Unsupported file type":
            return "Unsupported file type"
        prompt = {
            "resume": self.resume_text,
            "job_description": self.job_description,
            "Instructions": "Please do exactly as I say and provide this information only. Tailor this resume to the provided job description. Include a two-sentence objective statement, skills section, 3-5 bullet points per role that describe impact and uses metrics. Incorporate the most important keywords from the job description, match action verbs and skills."
        }
        return self.call_openai_api(prompt, max_tokens=5000) #See above for changing number of tokens

- First we check if the received files are in acceptable format, then we process the prompt. 
- prompt = {
            "resume": self.resume_text, #stored extracted resume
            "job_description": self.job_description, #stored extracted resume
            "Instructions": "Your prompt" #Type the prompt you want to send to ChatGPT
        }
- return self.call_openai_api(prompt, max_tokens=5000) #Set max number of tokens that you want to be used. It should vary based on the complexity of the prompt.

4. Generate Interview Questions, Identify Gaps and Candidate's Strengths:
    - 10 generic and job-specific questions are produced based on both the resume and job description.
    - The model is capable of identifying gaps in skills, knowledge and experience and highlight the missing requirements.
    - Based on the resume and job description, identify stengths that candidate should focus on when presenting themselves in an interview. 

5. Develop personalized learning plan:
    - Based on the gaps, user's resume and prospective job description, develop a learning plan how the user can obtain the required skills and knowledge to successfully compete for the role. 
    - The plan includes short-term, mid-term, and long-term development and ranges from tutorials and courses to self-learning portfolio projects and formal education. 

## Installation
1. Clone the repository. 
2. pip install openai pdfplumber python-docx Flask 
3. Obtain OpenAI API key and place it between " " here: career_coach = CareerCoachAI(api_key="your_api_key")
4. #CHANGE Start flask server using next commands: 
    set FLASK_APP=app.py
    flask run

## Usage
Once installed and resume and cover letter are provided, use the following requests:


## Further Improvements

The project will go through several iterations of improvements. Current planned changes:
1. Connect CareerCoachAI to backend.
2. Connect CareerCoachAI to Intercom chatbot.
3. (AFTER CONNECTED TO INTERCOM) Change the filepath takein back to file after demo.
4. (IN PROGRESS) Change format/structure of the output to always receive responses in the same format.
5. (DONE)Send json instead of text - RECHECK.
6. (DONE)Erase \n \t extracted from the resume (or from the output) - NEED TO RECHECK AGAIN.
7. (DONE)Store resume and job description prior to calling the functions instead of calling them within the functions to save time.
8. (DONE)Change # of tokens for each function - 5000 is enough for resume, but others need less.
9. (NOT STARTED) Rework the following route/function to accept and process uploaded files:
    @app.route('/api/store_texts', methods=['POST'])
        def store_texts():
10. Instead of providing OpenAI key directly, link to the appropriate location of the key.

### The Core Team
Project completed by Nikita Gaidamachenko.