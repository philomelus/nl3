
from flask import Blueprint

from nl3.auth import login_required


bp = Blueprint('changes', __name__, url_prefix='/changes')


@login_required
@bp.route('/notes', methods=('GET', 'POST'))
def notes():
    pass


@login_required
@bp.route('/history', methods=('GET', 'POST'))
def history():
    pass


@login_required
@bp.route('/report', methods=('GET', 'POST'))
def report():
    pass

