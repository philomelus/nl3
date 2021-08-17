
from flask import Blueprint

from nl.routes import changes, reports


bp = Blueprint('routes', __name__, url_prefix='/routes')

bp.register_blueprint(changes.bp)
bp.register_blueprint(reports.bp)


from nl.routes import handlers

