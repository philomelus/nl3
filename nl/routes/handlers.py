
from flask_login import login_required

from nl.routes import bp


@bp.route('/sequencing', methods=('GET', 'POST'))
@login_required
def sequencing():
    pass

