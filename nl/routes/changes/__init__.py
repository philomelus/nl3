
from flask import Blueprint


bp = Blueprint('changes', __name__, url_prefix='/changes')


from nl.routes.changes import handlers

