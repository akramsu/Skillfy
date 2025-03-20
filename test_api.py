import os
import google.generativeai as genai
from dotenv import load_dotenv

# Load environment variables
load_dotenv()
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
print(f"API Key: {GEMINI_API_KEY[:5]}...{GEMINI_API_KEY[-5:]}")

# Configure Gemini API
genai.configure(api_key=GEMINI_API_KEY)

def test_gemini_api():
    try:
        # Use a specific model from the list we saw
        model_name = "models/gemini-1.5-pro-latest"
        print(f"Using model: {model_name}")
        
        # Create the model
        model = genai.GenerativeModel(model_name=model_name)
        
        # Simple test prompt
        prompt = "Hello, can you confirm that the Gemini API is working correctly?"
        
        # Generate a response using Gemini
        response = model.generate_content(prompt)
        
        # Print the response
        print("API TEST SUCCESSFUL!")
        print("Response from Gemini API:")
        print(response.text)
        return True
        
    except Exception as e:
        print("API TEST FAILED!")
        print(f"Error with Gemini API: {str(e)}")
        return False

if __name__ == "__main__":
    print("Testing Gemini API connection...")
    test_gemini_api()
