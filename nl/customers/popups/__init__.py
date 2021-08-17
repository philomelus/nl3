
from flask import Blueprint

bp = Blueprint('popups', __name__, url_prefix='/popups')

from nl.customers.popups import handlers

