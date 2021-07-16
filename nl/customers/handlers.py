
from flask_login import login_required

from nl.customers import bp


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    pass


@bp.route('/flagstops', methods=('GET', 'POST'))
@login_required
def flagstops():
    pass


@bp.route('/addnew', methods=('GET', 'POST'))
@login_required
def addnew():
    pass


@bp.route('/combined', methods=('GET', 'POST'))
@login_required
def combined():
    pass

