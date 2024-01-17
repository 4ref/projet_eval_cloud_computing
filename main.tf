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

resource "azurerm_mysql_server" "agng-serveur" {
  name                = "agng-mysqlserver"
  location            = azurerm_resource_group.rg.location
  resource_group_name = azurerm_resource_group.rg.name

  administrator_login          = "agng"
  administrator_login_password = "Password.2024"

  sku_name   = "GP_Gen5_2"
  storage_mb = 5120
  version    = "5.7"

  auto_grow_enabled                 = true
  backup_retention_days             = 7
  geo_redundant_backup_enabled      = true
  infrastructure_encryption_enabled = true
  public_network_access_enabled     = false
  ssl_enforcement_enabled           = true
  ssl_minimal_tls_version_enforced  = "TLS1_2"
}

resource "azurerm_mysql_database" "agng-database" {
  name                = "agngdb"
  resource_group_name = azurerm_resource_group.rg.name
  server_name         = azurerm_mysql_server.agng-serveur.name
  charset             = "utf8"
  collation           = "utf8_unicode_ci"
  # prevent the possibility of accidental data loss
  lifecycle {
    prevent_destroy = true
  }
}