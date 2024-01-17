# Define the required Terraform provider and version
terraform {
  required_providers {
    azurerm = {
      source  = "hashicorp/azurerm"
      version = "~> 3.87.0"
    }
  }
  required_version = ">= 0.14.9"
}

# Configure the Azure provider
provider "azurerm" {
  features {}
}

# Create an Azure resource group
resource "azurerm_resource_group" "rg" {
  name     = "AGNG_resource_group"
  location = "eastus"
}

# Define an App Service plan for the web app
resource "azurerm_service_plan" "appserviceplan" {
  name                = "AGNG-webapp-asp"
  location            = azurerm_resource_group.rg.location
  resource_group_name = azurerm_resource_group.rg.name
  os_type             = "Linux"
  sku_name            = "B1"
}

# Create an Azure Linux web app
resource "azurerm_linux_web_app" "webapp" {
  name                = "AGNG-webapp"
  location            = azurerm_resource_group.rg.location
  resource_group_name = azurerm_resource_group.rg.name
  service_plan_id     = azurerm_service_plan.appserviceplan.id
  https_only          = true
  site_config {
    minimum_tls_version = "1.2"
  }
}

resource "azurerm_app_service_source_control" "sourcecontrol" {
  app_id                  = azurerm_linux_web_app.webapp.id
  repo_url                = "https://github.com/4ref/projet-eval-symfony"
  branch                  = "main"
  use_manual_integration  = true
  use_mercurial           = false
}
