const plugins = {};

document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector("#sidebar");

    if (sidebar) {
        sidebar.addEventListener("click", (e) => {
            let toolButton = null;

            if (e.target.classList.contains("tool-button")) {
                toolButton = e.target;
            } else {
                toolButton = e.target.closest(".tool-button");
            }

            if (toolButton) {
                selectPlugin(toolButton.dataset.plugin);
            }
        });
    }

    const createPluginButton = document.getElementById("createPlugin");
    const newPluginButton = document.getElementById("newPlugin");
    const editPluginButton = document.getElementById("editPlugin");

    if (newPluginButton) {
        newPluginButton.addEventListener("click", function () {
            const start = document.getElementById("start");
            if (start) {
                start.style.display = "none";
            }
            showModal("#aiInteraction");
            document.getElementById("aiInteractionForm").style.display = "block";
            createPluginButton.innerText = "Submit";
            createPluginButton.disabled = false;
        });
    }

    if (editPluginButton) {
        editPluginButton.addEventListener("click", function () {
            var tool_name_input = document.getElementById("edit_tool_name");
            var tool_description_input = document.getElementById("edit_tool_description");

            if (tool_name_input.validity.valueMissing) {
                console.error("Missing edit tool name");
                return;
            }
            if (tool_description_input.validity.valueMissing) {
                console.error("Missing tool edit instructions");
                return;
            }

            var tool_name = tool_name_input.value;
            var tool_description = tool_description_input.value;

            console.log("Editing plugin with AI");
            showProgress("#editPluginProgressContainer");
            document.getElementById("aiImproveForm").style.display = "none";
            this.innerText = "Please wait...";
            this.disabled = true;

            const status = document.getElementById("editPluginStatus");
            const roboDev = getRandomRoboDev();
            status.innerText = "Finding an available AI Developer...";

            const fader = new TextFader(
                "editPluginStatus",
                [
                    "Ok, found available AI developer...",
                    roboDev + " has been assigned to edit your tool!",
                    roboDev + " is reading your instructions...",
                    "Almost there...",
                    "Just testing the code ...",
                ],
                30000
            );
            fader.start();
            
            fetch("/code/" + tool_name, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    name: tool_name,
                    description: tool_description,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (typeof data.pluginCode != "undefined") {
                        loadPluginDynamically(data.pluginCode);
                        loadPlugin(data.tool);

                        status.style.fontWeight = "bold";
                        status.style.color = "green";
                        status.innerHTML = "<i class='fa fa-check'></i> It worked! Enjoy your " + data.tool;
                        fader.stop();

                        setTimeout(() => {
                            hideModal();
                            setTimeout(() => {
                                showModal("#newToolSuccess");
                                setTimeout(() => hideModal(), 2000);
                                status.style.color = "black";
                            }, 30);
                        }, 1000);
                    } else {
                        const error = data.error || "Bad news! The code failed testing. Sadly, it has been deleted...try again?";
                        console.error(error);
                        status.style.fontWeight = "bold";
                        status.style.color = "red";
                        status.innerText = error;
                        fader.stop();
                    }

                    hideProgress("#editPluginProgressContainer");
                });
        });
    }

    if (createPluginButton) {
        createPluginButton.addEventListener("click", function (e) {
            e.preventDefault();

            var tool_name_input = document.getElementById("tool_name");
            var tool_description_input = document.getElementById("tool_description");

            if (tool_name_input.validity.valueMissing) {
                alert("Invalid name");
                return;
            }
            if (tool_description_input.validity.valueMissing) {
                alert("Invalid description");
                return;
            }

            var tool_name = tool_name_input.value;
            var tool_description = tool_description_input.value;

            console.log("Loading new plugin with AI");
            showProgress("#newPluginProgressContainer");
            document.getElementById("aiInteractionForm").style.display = "none";
            this.innerText = "Please wait...";
            this.disabled = true;

            const status = document.getElementById("createPluginStatus");
            const roboDev = getRandomRoboDev();
            status.innerText = "Finding an available AI Robot Developer...";

            const fader = new TextFader(
                "createPluginStatus",
                [
                    "Ok, found available AI developer...",
                    roboDev + " has been assigned to build your tool!",
                    roboDev + " is reading your instructions...",
                    "Almost there...",
                    "Just testing the code ...",
                ],
                30000
            );
            fader.start();

            fetch("/code", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    name: tool_name,
                    description: tool_description,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (typeof data.pluginCode != "undefined") {
                        loadPluginDynamically(data.pluginCode);
                        loadPlugin(data.tool);

                        status.style.fontWeight = "bold";
                        status.style.color = "green";
                        status.innerHTML = "<i class='fa fa-check'></i> It worked! Enjoy your " + data.tool;
                        fader.stop();

                        setTimeout(() => {
                            hideModal();
                            setTimeout(() => {
                                showModal("#newToolSuccess");
                                setTimeout(() => hideModal(), 2000);
                                status.style.color = "black";
                            }, 30);
                        }, 1000);
                    } else {
                        const error = data.error || "Bad news! The code failed testing. Sadly, it has been deleted...try again?";
                        console.error(error);
                        status.style.fontWeight = "bold";
                        status.style.color = "red";
                        status.innerText = error;
                        fader.stop();
                    }

                    hideProgress("#newPluginProgressContainer");
                });
        });
    }

    function loadPlugin(pluginDefinition) {
        const pluginTest = new PluginTester(pluginDefinition, plugins[pluginDefinition]);
        pluginTest.run();

        if (!pluginTest.passed) {
            console.error(`Plugin tests failed, cannot load [${pluginDefinition}]`);
            return;
        }

        const plugin = new plugins[pluginDefinition](pluginDefinition);

        if (typeof plugins[name] == "undefined") {
            plugins[plugin.name] = plugin;
            plugin.activate();
            selectPlugin(pluginDefinition);
        } else {
            console.log("plugin [" + name + "] already loaded");
        }
    }

    function loadPluginDynamically(pluginCode) {
        eval(pluginCode);
    }

    function loadExistingPlugin(plugin) {
        console.log("Loading existing plugin [" + plugin + "]");
        fetch("/code/" + plugin, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (typeof data.pluginCode != "undefined") {
                    loadPluginDynamically(data.pluginCode);
                    loadPlugin(data.tool);
                } else {
                    console.error(data.error || "Plugin cannot be loaded");
                }
            });
    }

    function deleteExistingPlugin(plugin) {
        console.log("Deleting existing plugin [" + plugin + "]");
        fetch("/code/" + plugin, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                name: plugin,
            })
        })
            .then((response) => response.json())
            .then((data) => {
                location.reload();
            });
    }

    function selectPlugin(pluginClass) {
        const previousPluginButton = sidebar.querySelector(".tool-button.selected");
        if (previousPluginButton) {
            previousPluginButton.classList.remove("selected");
            const previousPlugin = plugins[previousPluginButton.dataset.plugin];
            previousPlugin.selected = false;
        }

        let toolButton = document.querySelector(".tool-button[data-plugin='" + pluginClass + "']");
        toolButton.classList.add("selected");

        const plugin = plugins[pluginClass];

        if (plugin) {
            plugin.selected = true;
            plugin.init("paintCanvas");

            document.querySelectorAll(".tool-custom-ui").forEach(el => {
                el.style.display = 'none';
            });

            const toolCustomUI = document.querySelector(".tool-custom-ui[data-name='" + plugin.name + "']");

            if (toolCustomUI) {
                toolCustomUI.style.display = 'block';
            }

            const improveBtn = document.getElementById("improvePlugin")
            improveBtn.style.display = "block";
            improveBtn.querySelector("span").innerText = "Edit " + pluginClass;
            document.getElementById("edit_tool_name").value = pluginClass;
            document.getElementById("edit_tool_description_label").querySelector("span").innerText = pluginClass;
            document.getElementById("aiImprove").querySelector("h2").innerText = 'Edit ' + pluginClass;
            setTimeout(() => {
                document.getElementById("edit_tool_description").focus();
            }, 1000);
        }
    }

    const loadPluginsButton = document.getElementById("loadPlugins");

    if (loadPluginsButton) {
        loadPluginsButton.addEventListener("click", () => {
            document.querySelectorAll("#plugins [data-plugin]").forEach((el) => {
                loadExistingPlugin(el.dataset.plugin);
            });

            document.getElementById("plugins").remove();
        });
    }

    document.querySelectorAll("#plugins [data-plugin]").forEach((el) => {
        el.addEventListener("click", (e) => {
            let plugin = e.currentTarget.dataset.plugin;
            loadExistingPlugin(plugin);
        });
    });

    document.querySelectorAll("#plugins [data-delete]").forEach((el) => {
        el.addEventListener("click", (e) => {
            if (confirm('Are you sure?')) {
                let plugin = e.currentTarget.dataset.delete;
                deleteExistingPlugin(plugin);
            }
        });
    });

    document.querySelector("#improvePlugin").addEventListener("click", el => {
        showModal("#aiImprove");
        document.getElementById("aiInteractionForm").style.display = "block";
        createPluginButton.innerText = "Submit";
        createPluginButton.disabled = false;
    });
});
