# ECM Flowchart Explained

```mermaid
swimlane-beta LR
  subgraph frontend [Frontend]
    FF[Fillable Forms]
    UC{Uniform Contract}
    UP[Upload Form]
  end

  subgraph api [API]
    create[Create Container]
    is_created{Is Container Created}
    store[Store in Container]
    move[Move container to queue]
    populate[Populate Metadata]
    notification[Notification]
    archive[Archive Container]
  end
  
  subgraph box [Box]
     Create
  end

  FF ---|Yes| UC ---|No| UP
  create ---|No|is_created -->|Yes|store --> move --- populate --- notification --- archive
```