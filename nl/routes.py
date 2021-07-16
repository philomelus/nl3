
from flask import render_template
from flask_login import login_required

from nl import app, db


@app.route('/')
@app.route('/index')
@login_required
def index():
    return render_template('index.html', title='Home')

