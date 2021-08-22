
from flask import request, flash, url_for
from flask_security import login_required

from nl import db
from nl.customers.combined.popups import bp


@bp.route('/add', methods=('POST',))
@login_required
def add():
    """
    """
    return '', 501

@bp.route('/create', methods=('POST',))
@login_required
def create():
    """
    """
    return '', 501

@bp.route('/delete', methods=('POST',))
@login_required
def delete():
    """
    """
    return '', 501

@bp.route('/update', methods=('POST',))
@login_required
def update():
    """
    """
    return '', 501

