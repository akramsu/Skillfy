import subprocess
import threading
import time
import webbrowser

def open_browser():
    """Open browser to home page"""
    time.sleep(1.5)  # Wait for Flask to start
    webbrowser.open('http://localhost:5000')

def run_flask():
    subprocess.run(['python', 'src/web_app.py'])

def run_streamlit():
    time.sleep(2)  # Wait for Flask to start
    subprocess.run(['streamlit', 'run', 'src/main.py'])

if __name__ == '__main__':
    # Start browser opener in a separate thread
    browser_thread = threading.Thread(target=open_browser)
    browser_thread.start()

    # Start Flask in a separate thread
    flask_thread = threading.Thread(target=run_flask)
    flask_thread.start()

    # Start Streamlit in a separate thread
    streamlit_thread = threading.Thread(target=run_streamlit)
    streamlit_thread.start()

    # Wait for all threads
    browser_thread.join()
    flask_thread.join()
    streamlit_thread.join()
