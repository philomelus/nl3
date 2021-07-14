
from flask import Blueprint
from nl3.auth import login_required


bp = Blueprint('reports', __name__, url_prefix='/reports')


@login_required
@bp.route('/draw', methods=('GET', 'POST'))
def draw():
    pass


@login_required
@bp.route('/route', methods=('GET', 'POST'))
def route():
    pass


@login_required
@bp.route('/status', methods=('GET', 'POST'))
def status():
    pass


@login_required
@bp.route('/tips', methods=('GET', 'POST'))
def tips():
    pass

