'use strict';

import bcrypt from 'bcrypt';

const {
  Model
} = require('sequelize');
module.exports = (sequelize, DataTypes) => {
  class Users extends Model {
    /**
     * Helper method for defining associations.
     * This method is not a part of Sequelize lifecycle.
     * The `models/index` file will call this method automatically.
     */
    static associate(models) {
      // define association here
    }
  };
  Users.init({
    name: DataTypes.STRING,
    email: DataTypes.STRING,
    password: DataTypes.STRING,
    apiKey: DataTypes.STRING
  }, {
    sequelize,
    modelName: 'Users',
    hooks: {
      beforeCreate: async user => {
        let userCheck = await Users.findAll({
          where: {
            email: user.email
          }
        });

        if (userCheck.length > 0) {
          throw "The e-mail already exist!";
        }

        const saltBcrypt = bcrypt.genSaltSync();
        user.password = bcrypt.hashSync(user.password, saltBcrypt);
      }
    }
  });

  Users.prototype.checkPassword = function (password) {
    return bcrypt.compareSync(password, this.password);
  }

  return Users;
};