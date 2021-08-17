
from flask import Blueprint


bp = Blueprint('reports', __name__, url_prefix='/reports')


from nl.customers.reports import handlers

