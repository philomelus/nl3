
from flask import Blueprint


bp = Blueprint('combined', __name__, url_prefix='/combined')


from nl.customers.combined import popups
bp.register_blueprint(popups.bp)


from nl.customers.combined import handlers

