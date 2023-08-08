module.exports = {
    moduleFileExtensions: ['js', 'json', 'vue'],
    transform: {
        '^.+\\.vue$': 'vue-jest',
        '^.+\\.js$': 'babel-jest'
    },
    snapshotSerializers: ['jest-serializer-vue'],
    testMatch: [
        '**/tests/unit/**/*.spec.js'
    ],
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/src/$1'
    }
};
