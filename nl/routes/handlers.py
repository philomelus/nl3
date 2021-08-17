
from flask import render_template
from flask_login import login_required

from nl.routes import bp


@bp.route('/sequencing', methods=('GET', 'POST'))
@login_required
def sequencing():
    return render_template('working.html', path='Routes / Sequencing')


