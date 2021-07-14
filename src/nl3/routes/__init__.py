
from flask import Blueprint

from nl3.auth import login_required
from nl3.routes import (changes, reports)


bp = Blueprint('routes', __name__, url_prefix='/routes')

bp.register_blueprint(changes.bp)
bp.register_blueprint(reports.bp)


@login_required
@bp.route('/sequencing', methods=('GET', 'POST'))
def sequencing():
    pass

