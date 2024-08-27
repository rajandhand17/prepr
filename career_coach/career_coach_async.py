#Considerations for future devs (as of August 2024):
#MAX TOKENS is 8162 - includes job description, resume AND the instruction.
#OpenAI CANNOT store resume, job description and other information for the user. It must be sent each time. 
#Accessing OpenAI API directly through their library DOES NOT allow sending concurrent API requests. It must be done using httpx or other similar solutions
#Asyncio can make function run concurrently, but NOT API requsts. Use HTTPX for asynchronous API calls. 
#Prompts need to be tailor for better, more consistent answers

#1. (DONE)Change # of tokens for each function - 5000 is enough for resume, but others need less
#2. (AFTER CONNECTED TO INTERCOM) Change the filepath takein back to file after demo
#3. (DONE)Store resume and job description prior to calling the functions instead of calling them within the functions to save time
#4. (DONE)Send json instead of text? Check how to keep the formatting in json
#5. (DONE)Erase \n \t extracted from the resume (or from the output)
#6. (IN PROGRESS) Change format/structure of the output to always receive responses in the same format
#7. (TO BE COMPLETED) UPDATE README

import json
import os
import httpx
import pdfplumber
import asyncio
from docx import Document

class CareerCoachAI:
    def __init__(self, api_key):
        self.api_key = api_key
        self.resume_text = None
        self.job_description = None

    async def call_openai_api(self, prompt_dict, max_tokens):
        prompt_json = json.dumps(prompt_dict)
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json"
        }
        data = {
            "model": "gpt-4",
            "messages": [
                {"role": "system", "content": "You are an experienced HR professional with extensive knowledge in tech, human resources, sales, marketing, finance, and budgeting. Provide answer in the same language as the job description or resume if description is not present"},
                {"role": "user", "content": prompt_json}
            ],
            "max_tokens": max_tokens
        }

        try:
            async with httpx.AsyncClient(timeout=180.0) as client: #adjust time out if needed (in seconds) timeout has to be over 1min so larger requests can be processed
                response = await client.post("https://api.openai.com/v1/chat/completions", headers=headers, json=data)
                response.raise_for_status()  #raise an exception for 400s/500s status codes
                response_data = response.json()
                return response_data['choices'][0]['message']['content']
        except httpx.ReadTimeout:
            return "The request timed out. Please try again."
        except httpx.RequestError as e:
            return f"An error occurred while sending the request: {str(e)}"

    def extract_text_from_pdf(self, file):
        text = ""
        with pdfplumber.open(file) as pdf:
            for page in pdf.pages:
                text += page.extract_text().replace('\n', ' ').replace('\t', ' ').replace('\u2022', ' ')
        return text

    def extract_text_from_doc(self, file):
        text = ""
        doc = Document(file)
        for paragraph in doc.paragraphs:
            text += paragraph.text.replace('\n', ' ').replace('\t', ' ').replace('\u2022', ' ')
        return text
    
    async def extract_text(self, file):
        file_extension = os.path.splitext(file)[1].lower()
        if file_extension == '.pdf':
            return self.extract_text_from_pdf(file)
        elif file_extension in ['.doc', '.docx']:
            return self.extract_text_from_doc(file)
        else:
            return "Unsupported file type"

    async def store_texts(self, resume_file, job_description_file):
        self.resume_text = await self.extract_text(resume_file)
        self.job_description = await self.extract_text(job_description_file)

    async def tailor_resume(self):
        if not self.resume_text or not self.job_description:
            return "Please provide both resume and job description first."

        prompt = {
            "resume": self.resume_text,
            "job_description": self.job_description,
            "Instructions": (
                """Tailor the resume to the provided job description. Include a two-sentence objective statement, "
                "a skills section, and 3-5 bullet points per role that describe impact with metrics. "
                "Incorporate the most relevant keywords from the job description, match action verbs, and skills."""
            )
        }
        return await self.call_openai_api(prompt, max_tokens=4000)

    async def tailor_cover_letter(self):
        if not self.resume_text or not self.job_description:
            return "Please provide both resume and job description first."
        
        prompt = {
            "resume": self.resume_text,
            "job_description": self.job_description,
            "Instructions": (
                "Draft a skill-based cover letter, focusing on 3–5 skills that align with the job description. "
                "Provide brief examples from the resume of how these skills were applied. "
                "Limit the cover letter to four paragraphs and a maximum of 500 characters, "
                "using professional language and adhering to best cover letter practices."
            )
        }
        return await self.call_openai_api(prompt, max_tokens=2000)

    async def gen_questions(self):
        if not self.resume_text or not self.job_description:
            return "Please provide both resume and job description first."
        
        prompt = {
            "resume": self.resume_text,
            "job_description": self.job_description,
            "Instructions": (
                "Generate a list of 10 interview questions based on the provided resume and job description. "
                "Include a mix of general and technical questions relevant to the job."
            )
        }
        return await self.call_openai_api(prompt, max_tokens=2000)

    async def identify_gaps(self):
        if not self.resume_text or not self.job_description:
            return "Please provide both resume and job description first."
        
        prompt = {
            "resume": self.resume_text,
            "job_description": self.job_description,
            "Instructions": (
                "Identify any gaps in the resume in relation to the job description. "
                "Highlight missing skills, responsibilities, education, and experience."
            )
        }
        return await self.call_openai_api(prompt, max_tokens=5000)

    async def identify_fit(self):
        if not self.resume_text or not self.job_description:
            return "Please provide both resume and job description first."
        
        prompt = {
            "resume": self.resume_text,
            "job_description": self.job_description,
            "Instructions": (
                "Assess the resume and job description to determine the candidate's fit. "
                "Highlight the unique blend of skills and experience that make the candidate a strong match for the job."
            )
        }
        return await self.call_openai_api(prompt, max_tokens=5000)
    
    async def learning_plan(self):
        if not self.resume_text or not self.job_description:
            return "Please provide both resume and job description first."
        
        prompt = {
            "resume": self.resume_text,
            "job_description": self.job_description,
            "Instructions": (
                "Develop a personalized learning plan to help the candidate secure the job. "
                "Include recommended courses, certifications, and areas of improvement, with both short-term and long-term steps. "
                "Provide tangible steps the candidate can take to enhance their qualifications."
            )
        }
        return await self.call_openai_api(prompt, max_tokens=5000)

def main():
    career_coach = CareerCoachAI(api_key="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")
    #Run one print test at a time to see the results to better navigate the text
    async def run_tasks():
        await career_coach.store_texts("NGaidamachenko_Resume.pdf", "Job_Description.docx")

        tailor_resume_task = career_coach.tailor_resume()
        learning_plan_task = career_coach.learning_plan()

        #Run tasks at the same time
        results = await asyncio.gather(tailor_resume_task, learning_plan_task)

        print("Tailored Resume:\n", results[0])
        print("\nLearning Plan:\n", results[1])

    asyncio.run(run_tasks())
if __name__ == "__main__":
    main()
