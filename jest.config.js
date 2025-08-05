module.exports = {
  testEnvironment: 'jsdom',
  transform: {
    '^.+\.js$': 'babel-jest',
  },
  moduleNameMapper: {
    '^@/(.*)$': '<rootDir>/assets/$1',
  },
  setupFilesAfterEnv: ['<rootDir>/jest.setup.js'],
};