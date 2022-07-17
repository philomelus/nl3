from flask import Blueprint


bp = Blueprint("reports", __name__, url_prefix="/reports")


from nl.routes.reports import handlers
