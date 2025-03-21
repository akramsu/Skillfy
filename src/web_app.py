from flask import Flask, render_template, redirect, url_for, send_from_directory

app = Flask(__name__, 
    template_folder='../Skillfy_AI',
    static_folder='../Skillfy_AI')

@app.route('/')
def home():
    return render_template('pages/home.html')

@app.route('/pages/<path:filename>')
def serve_pages(filename):
    return send_from_directory('../Skillfy_AI/pages', filename)

@app.route('/assets/<path:filename>')
def serve_assets(filename):
    return send_from_directory('../Skillfy_AI/assets', filename)

@app.route('/styles/<path:filename>')
def serve_styles(filename):
    return send_from_directory('../Skillfy_AI/styles', filename)

@app.route('/js/<path:filename>')
def serve_js(filename):
    return send_from_directory('../Skillfy_AI/js', filename)

@app.route('/create_resume')
def create_resume():
    return redirect('http://localhost:8501')

@app.route('/cv_analysis')
def cv_analysis():
    return redirect('http://localhost:8501/?page=cv_analysis')

if __name__ == '__main__':
    app.run(port=5000, debug=True, use_reloader=False)
