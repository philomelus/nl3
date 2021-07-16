
from flask import render_template
from flask_login import login_required

from nl.main import bp


@bp.route('/')
@bp.route('/index')
@login_required
def index():
    return render_template('index.html', title='Home')


@bp.route('/profile')
@login_required
def profile():
    pass

