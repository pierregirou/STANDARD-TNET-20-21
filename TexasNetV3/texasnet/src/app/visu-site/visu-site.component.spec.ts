import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { VisuSiteComponent } from './visu-site.component';

describe('VisuSiteComponent', () => {
  let component: VisuSiteComponent;
  let fixture: ComponentFixture<VisuSiteComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ VisuSiteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(VisuSiteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
