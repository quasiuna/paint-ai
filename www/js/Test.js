class PluginTester {
  constructor(name, plugin) {
    this.pluginName = name;
    this.plugin = plugin;
    this.passed = true;
  }

  assert(condition, message) {
    if (!condition) {
      throw new Error(message || "Assertion failed");
    }
  }

  reportTestResult(testName, testFunction) {
    try {
      this[testFunction]();
      console.log(`Test passed: [${this.pluginName}] ${testName}`);
    } catch (error) {
      this.passed = false;
      console.error(`Test failed: [${this.pluginName}] ${testName}`, error);
    }
  }

  testConstructor() {
    const pluginInstance = new this.plugin("testname");
    this.assert(pluginInstance.name === "testname", "Constructor name assignment failed");
    this.assert(pluginInstance.description !== "Example Description", "Constructor description assignment failed");
    this.assert(pluginInstance.icon !== null, "Constructor icon assignment failed");
  }

  testMethodInheritanceAndOverrides() {
    const pluginInstance = new this.plugin("testname");

    // Inherited methods
    this.assert(typeof pluginInstance.getMousePos === "function", "getMousePos method not inherited");
    this.assert(typeof pluginInstance.startPainting === "function", "startPainting method not inherited");

    // Overridden methods
    this.assert(typeof pluginInstance.paint === "function", "paint method not overridden");
  }

  testPropertyInheritance() {
    const pluginInstance = new this.plugin("testname");
    this.assert(pluginInstance.canvas === null, "canvas property not inherited");
    this.assert(pluginInstance.ctx === null, "ctx property not inherited");
    this.assert(pluginInstance.painting === false, "painting property not inherited");
    this.assert(pluginInstance.selected === false, "selected property not inherited");
  }

  run() {
    this.reportTestResult("Constructor Inheritance", "testConstructor");
    this.reportTestResult("Method Inheritance and Overrides", "testMethodInheritanceAndOverrides");
    this.reportTestResult("Property Inheritance", "testPropertyInheritance");
  }
}
