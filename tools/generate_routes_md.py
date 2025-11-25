import json
import os

file_path: str = input("Insira o caminho do arquivo .ini: ")

file_content: list[str] = []
with open(file_path, "r") as file:
    file_content = file.readlines()


routes_dict: dict = {}

authorized: bool = False
route: str = ""
method: str = ""
for line_idx, line in enumerate(file_content):
    line = line.removesuffix("\n") 
    if line.startswith("["):
        authorized = False
        line = line.removeprefix("[").removesuffix("]")
        line_split: list[str] = line.split(" ")
        
        idx_offset: int = 0
        # (a rota requer autorização)
        if len(line_split) == 3:
            if line_split[0] != "*":
                print(f"Tem alguma coisa errada na rota da linha {line_idx} | Line content: {line}")

            authorized = True
            idx_offset = 1

        method = line_split[0 + idx_offset]
        route = line_split[1 + idx_offset]
        routes_dict[route] = {
            "method": method,
            "authorized": authorized,
            "body_parameters": {},
            "optional_body_parameters": {}
        }
        continue
    
    if line.startswith("body"):
        json_str: str = line.split("=")[-1].strip().removeprefix("'").removesuffix("'")
        body_data: dict[str, str] = json.loads(json_str)
        
        for key in body_data:
            optional_parameter: bool = key.startswith("?")
            parameter_type: str = body_data[key]
            if optional_parameter:
                key = key.removeprefix("?")
                routes_dict[route]["optional_body_parameters"][key] = {"type": parameter_type}
                continue

            routes_dict[route]["body_parameters"][key] = {"type": parameter_type}

sorted_dict = dict(sorted(routes_dict.items()))
md_content: str = ""
for route_name in sorted_dict: 
    route_md: str = ""
    route_md += f"### {routes_dict[route_name]["method"]} ``{route_name}``"
    if routes_dict[route_name]["authorized"]:
        route_md += " **Authenticated**"

    route_md += "\n"
    
    if routes_dict[route_name]["body_parameters"] != {}:
        route_md += "**Required Body Parameters:** \n"
        for parameter in routes_dict[route_name]["body_parameters"]:
            route_md += f"- {parameter}: ``{routes_dict[route_name]["body_parameters"][parameter]["type"]}``\n"
        
    if routes_dict[route_name]["optional_body_parameters"] != {}:
        route_md += "**Optional Body Parameters:** \n"
        for parameter in routes_dict[route_name]["optional_body_parameters"]:
            route_md += f"- {parameter}: ``{routes_dict[route_name]["optional_body_parameters"][parameter]["type"]}``\n"

    md_content += route_md + "\n"

with open(os.path.dirname(__file__) + "/temp/temp_routes.md", "w") as file:
    file.write(md_content)