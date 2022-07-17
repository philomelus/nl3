from flask import Blueprint


bp = Blueprint("admin", __name__, url_prefix="/admin")

from nl.admin import customers

bp.register_blueprint(customers.bp)

from nl.admin import handlers
