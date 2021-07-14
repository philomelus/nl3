
from flask import Blueprint
from nl3.db import get_db
from nl3.auth import login_required


bp = Blueprint('profile', __name__, url_prefix='/profile')


@login_required
@bp.route('/', methods=('GET', 'POST'))
def profile():
    pass

