
from flask_login import login_required

from nl.routes.reports import bp


@bp.route('/draw', methods=('GET', 'POST'))
@login_required
def draw():
    pass


@bp.route('/route', methods=('GET', 'POST'))
@login_required
def route():
    pass


@bp.route('/status', methods=('GET', 'POST'))
@login_required
def status():
    pass


@bp.route('/tips', methods=('GET', 'POST'))
@login_required
def tips():
    pass

