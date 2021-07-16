
from flask import Blueprint


bp = Blueprint('main', __name__)


from nl.main import routes

