
from flask import Blueprint


bp = Blueprint('api', __name__)


from nl.api import customers


bp.register_blueprint(customers.bp, url_prefix='/customers')

