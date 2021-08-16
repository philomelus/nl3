# coding: utf-8

from flask_security import RoleMixin, UserMixin

from nl import db


class Role(db.Model, RoleMixin):
    """
    Represents virtual permissions that a user needs in order to access/use
    the resource/page/object named by role.
    """
    __tablename__ = 'auth_roles'
    
    id = db.Column(db.Integer, primary_key=True)
    desc = db.Column(db.String())
    name = db.Column(db.String(80), unique=True)
    permissions = db.Column(db.UnicodeText)


class RolesUsers(db.Model):
    """
    Associates Users with Roles, providing unlimited permission posibilities.
    """
    __tablename__ = 'auth_roles_users'
    
    id = db.Column(db.Integer, primary_key=True)
    role_id = db.Column(db.ForeignKey('auth_roles.id'), nullable=False)
    user_id = db.Column(db.ForeignKey('auth_users.id'), nullable=False)

    
class User(db.Model, UserMixin):
    """
    Users of newsledger.  NOT customers (well, they could be both ...).
    """
    __tablename__ = 'auth_users'

    id = db.Column(db.Integer, primary_key=True)
    active = db.Column(db.Boolean, nullable=False)
    email = db.Column(db.String(255), nullable=False, unique=True)
    fs_uniquifier = db.Column(db.String(64), nullable=False, unique=True)
    password = db.Column(db.String(255), nullable=False)
    username = db.Column(db.String(64), nullable=False, unique=True)

    roles = db.relationship('Role', secondary='auth_roles_users', backref='users')

    
# class Group1(db.Model):
#     """
#     Groups to allow common security and profile settings for classes of users.
#
#     LIMITATION:  Currently only a SINGLE group allowed per user ...
#                  which reduces the usefulness of this considerably...
#
#     DEPRECATED in 3.0
#     """
#     __tablename__ = 'groups'
#
#     id = db.Column(db.SmallInteger, primary_key=True)
#     name = db.Column(db.String(), nullable=False)
#
#     def __repr__(self):
#         return '<Group {}:{}>'.format(self.name, self.id)


# class Security1(db.Model):
#     """
#     For a specific page and feature, whether a particular user/group/anybody is allowed
#     to acually access/use the page/feature.
#
#     If group_id and user_id are Null, then effect is global.
#     If group_id is Null and user_id specified, effect is user specific.
#     If group_id is specified and user_id is Null, effect is group specific.
#
#     If group_id and user_id are specified, security failures will occur.
#
#     DEPRECATED in 3.0
#     """
#     __tablename__ = 'security'
#
#     group_id = db.Column(db.ForeignKey('Group.id', ondelete='CASCADE', onupdate='CASCADE'),
#                          nullable=True, primary_key=True)
#     user_id = db.Column(db.ForeignKey('User.id', ondelete='CASCADE', onupdate='CASCADE'),
#                         nullable=True, primary_key=True)
#     page = db.Column(db.String(20), nullable=False, primary_key=True)
#     feature = db.Column(db.String(20), nullable=False, primary_key=True)
#     allowed = db.Column(db.Enum('N', 'Y'), nullable=False)  
    

# class User1(db.Model, UserMixin):
#     """
#     Users of newsledger.  NOT customers (well, they could be both ...).
#
#     DEPRECATED in 3.0
#     """
#     __tablename__ = 'users'
#
#     id = db.Column(db.SmallInteger, primary_key=True)
#     login = db.Column(db.String(20), nullable=False)
#     password = db.Column(db.String(255), nullable=False)
#     name = db.Column(db.String(30), nullable=False)
#     group_id = db.Column(db.ForeignKey('groups.id', ondelete='CASCADE', onupdate='CASCADE'),
#                          nullable=False, index=True)
#     home = db.Column(db.String(255), nullable=False)
#
#     group = db.relationship('Group', primaryjoin='User.group_id == Group.id', backref='users')
#
#     def __repr__(self):
#         return '<User {}:{}>'.format(self.login, self.id)
#
#     def set_password(self, password):
#         self.password = generate_password_hash(password)
#
#     def check_password(self, password):
#         return check_password_hash(self.password, password)

