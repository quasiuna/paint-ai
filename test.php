<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paint AI Test</title>
</head>
<body>
    <script src="/js/Plugins.js"></script>
    <script src="/js/Tool.js"></script>
    <script src="/js/plugins/PenTool.js"></script>
    <script src="/js/Test.js"></script>
    <script>
    const pluginTest = new PluginTester(plugins.PenTool);
    pluginTest.run();
    console.log(pluginTest);
    </script>
</body>
</html>
