
from flask_login import login_required

from nl.routes.changes import bp


@bp.route('/notes', methods=('GET', 'POST'))
@login_required
def notes():
    pass


@bp.route('/history', methods=('GET', 'POST'))
@login_required
def history():
    pass


@bp.route('/report', methods=('GET', 'POST'))
@login_required
def report():
    pass

